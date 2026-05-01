<?php

namespace App\Http\Controllers;

use App\Question;
use App\User;
use Illuminate\Http\Request;
use App\Exam;
use Illuminate\Support\Facades\Http;

class ExamManagementController extends Controller
{
    // Menampilkan daftar semua ujian
    public function index()
    {
        $exams = Exam::all();
        return view('exams.index', compact('exams'));
    }

    // Menampilkan form tambah ujian
    public function create()
    {
        return view('exams.create');
    }

    // Menyimpan data ujian baru ke database
    public function store(Request $request)
    {
        $request->validate([
            'name'             => 'required|string|max:255',
            'type'             => 'required|in:mbti,disc,vak,epps,papi,big_five',
            'duration_minutes' => 'required|integer|min:1',
            'description'      => 'nullable|string',
            'question_file'    => 'required|mimes:pdf|max:5000',
        ]);

        // 1. Simpan Data Ujian ke Database
        $exam = Exam::create([
            'name'             => $request->name,
            'type'             => $request->type,
            'duration_minutes' => $request->duration_minutes,
            'description'      => $request->description,
        ]);

        // 2. Kirim PDF ke Python Service
        if ($request->hasFile('question_file')) {
            $file = $request->file('question_file');

            try {
                $response = \Illuminate\Support\Facades\Http::attach(
                    'file',
                    file_get_contents($file),
                    $file->getClientOriginalName()
                )->post('http://127.0.0.1:8001/extract-pdf');

                if ($response->successful()) {
                    $result    = $response->json();
                    $pdfType   = $result['type'] ?? 'vak';   // 'disc' atau 'vak'
                    $questions = $result['data'] ?? [];

                    // 3. Simpan tiap soal sesuai format PDF-nya
                    foreach ($questions as $index => $q) {

                        if ($pdfType === 'disc') {
                            // Format DISC: { "box": 1, "options": { "A": "...", "B": "...", ... } }
                            $boxNumber    = $q['box'] ?? ($index + 1);
                            $questionText = "Box " . $boxNumber;
                            $options      = $q['options'] ?? [];

                        } else {
                            // Format VAK: { "question": "1. Ketika ...", "options": { "A": "...", ... } }
                            $questionText = $q['question'] ?? '';
                            $number       = filter_var($questionText, FILTER_SANITIZE_NUMBER_INT) ?: ($index + 1);
                            $options      = $q['options'] ?? [];
                        }

                        Question::create([
                            'exam_id'       => $exam->id,
                            'number'        => $pdfType === 'disc' ? $boxNumber : (int) $number,
                            'question_text' => $questionText,
                            'options'       => $options,   // cast ke JSON otomatis
                        ]);
                    }

                    return redirect()->route('manage-exams.index')
                        ->with('success', "Ujian berhasil dibuat dan " . count($questions) . " soal otomatis diimpor.");

                } else {
                    return redirect()->back()
                        ->with('error', 'Python gagal memproses PDF. Pastikan format PDF benar.');
                }

            } catch (\Exception $e) {
                return redirect()->back()
                    ->with('error', 'Gagal terhubung ke Python Service: ' . $e->getMessage());
            }
        }

        return redirect()->route('manage-exams.index')
            ->with('success', 'Ujian berhasil ditambahkan tanpa soal.');
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $users = User::all(); // Ambil semua user untuk dipilih

        // Ambil ID user yang sudah terdaftar di ujian ini untuk menandai checkbox
        $assignedUserIds = $exam->users->pluck('id')->toArray();

        return view('admin.exams.peserta', compact('exam', 'users', 'assignedUserIds'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:mbti,disc,vak,epps,papi,big_five',
            'duration_minutes' => 'required|integer|min:1',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'exists:users,id',
        ]);

        $exam = Exam::findOrFail($id);

        // Update data ujian
        $exam->update($request->only(['name', 'type', 'duration_minutes', 'description']));

        // Sinkronisasi peserta
        $exam->users()->sync($request->input('user_ids', []));

        // Pastikan nama route sesuai dengan web.php (manage-exams.index)
        return redirect()->route('manage-exams.index')->with('success', 'Ujian dan peserta berhasil diperbarui.');
    }

    public function resultsIndex()
    {
        // Ambil semua sesi yang sudah selesai atau sedang berlangsung
        $sessions = \App\ExamSession::with(['user', 'exam'])
                    ->orderBy('created_at', 'desc')
                    ->get();

        return view('admin.exams.results_index', compact('sessions'));
    }

    public function resultsShow($session_id)
    {
        $session = \App\ExamSession::with(['user', 'exam.questions', 'proctoringLogs', 'userAnswers'])
                    ->findOrFail($session_id);

        // Di sini nanti kamu bisa tambahkan logika hitung skor DISC/MBTI
        // berdasarkan data dari $session->answers

        return view('admin.exams.results_show', compact('session'));
    }
}
