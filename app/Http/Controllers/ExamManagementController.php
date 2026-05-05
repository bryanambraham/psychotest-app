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

    public function edit(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $numbers = ['20', '40', '60', '80', '100'];
        $number_paginate = in_array($request->number, $numbers) ? $request->number : 20;

        // Mulai query dari model User
        $query = User::query();

        // Jika ada pencarian
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        // Ambil data user (gunakan paginate agar tidak berat jika user banyak)
        $users = $query->paginate($number_paginate);
        $users->appends($request->all());

        // Ambil ID user yang sudah terdaftar
        $assignedUserIds = $exam->users->pluck('id')->toArray();

        return view('admin.exams.peserta', compact('exam', 'users', 'assignedUserIds', 'number_paginate', 'number'));
    }

    public function update(Request $request, $id)
    {
    $exam = Exam::findOrFail($id);

    // 1. Ambil ID user yang sedang dicentang
    $selectedIds = $request->input('user_ids', []); // ID yang dicheck

    // 2. Ambil ID user yang sedang ditampilkan di layar (dari hidden input tadi)
    $visibleIds  = $request->input('visible_user_ids', []);

    // 3. Cari user mana yang ditampilkan tapi TIDAK dicentang (berarti ingin dihapus)
    $idsToRemove = array_diff($visibleIds, $selectedIds);

    // 4. Proses Update Database
    // Tambahkan user yang dicentang (tanpa menghapus yang lama/yang tidak tampil)
    if (!empty($selectedIds)) {
        $exam->users()->syncWithoutDetaching($selectedIds);
    }

    // Hapus hanya user yang tampil di layar tapi tidak dicentang
    if (!empty($idsToRemove)) {
        $exam->users()->detach($idsToRemove);
    }

    // Update detail ujian lainnya
    $exam->update([
        'name' => $request->input('name', $exam->name),
        'type' => $request->input('type', $exam->type),
        'duration_minutes' => $request->input('duration_minutes', $exam->duration_minutes),
    ]);

    $exam->refresh();

    // // Ambil semua nama user yang terdaftar di ujian ini
    // $userNames = $exam->users()->pluck('name')->implode(', ');


    return redirect()->back()->with('success', 'Daftar peserta ' . ' berhasil diperbarui.');
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
