<?php

namespace App\Http\Controllers;

use App\Question;
use App\User;
use App\VerifyUser;
use Illuminate\Http\Request;
use App\Exam;
use Illuminate\Support\Facades\Http;

class ExamManagementController extends Controller
{
    // Menampilkan daftar semua ujian
    public function index()
    {
        $exams = Exam::latest()->paginate(10);
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
            'type'             => 'required|in:mbti,disc,vak,epps,papi,big_five,pg_akuntansi,kasus_akuntansi,uraian,angka',
            'duration_minutes' => 'required|integer|min:1',
            'description'      => 'nullable|string',
            'question_file'    => 'required|mimes:pdf|max:5000',
        ]);

        $exam = Exam::create([
            'name'             => $request->name,
            'type'             => $request->type,
            'duration_minutes' => $request->duration_minutes,
            'description'      => $request->description,
        ]);

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
                    $pdfType   = $result['type'] ?? 'pilgan';
                    $questions = $result['data'] ?? [];

                    foreach ($questions as $index => $q) {
                        if ($pdfType === 'disc') {
                            $boxNumber    = $q['box'] ?? ($index + 1);
                            $questionText = "Box " . $boxNumber;
                            $options      = $q['options'] ?? [];
                            $number       = $boxNumber;
                        } else {
                            // Untuk angka, simpan seluruh struktur sebagai JSON
                            if ($pdfType === 'angka' && isset($q['table'])) {
                                $questionText = json_encode($q);
                            } else {
                                $questionText = $q['question'] ?? '';
                            }
                            
                            // Pengambilan nomor soal yang aman dari angka di dalam teks soal
                            $number = preg_match('/^(\d+)/', trim($q['question'] ?? ''), $matches) ? $matches[1] : ($index + 1);
                            $options      = $q['options'] ?? [];
                        }

                        Question::create([
                            'exam_id'       => $exam->id,
                            'number'        => (int) $number,
                            'question_text' => $questionText,
                            'options'       => $options,
                        ]);
                    }

                    return redirect()->route('manage-exams.index')
                        ->with('success', "Ujian berhasil dibuat dan " . count($questions) . " soal otomatis diimpor.");

                } else {
                    return redirect()->back()->with('error', 'Python gagal memproses PDF. Pastikan format PDF benar.');
                }

            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal terhubung ke Python Service: ' . $e->getMessage());
            }
        }

        return redirect()->route('manage-exams.index')->with('success', 'Ujian berhasil ditambahkan tanpa soal.');
    }

    public function edit(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);
        $numbers = ['20', '40', '60', '80', '100'];
        $number_paginate = in_array($request->number, $numbers) ? $request->number : 20;

        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $users = $query->paginate($number_paginate);
        $users->appends($request->all());

        $assignedUserIds = $exam->users->pluck('id')->toArray();

        // Variabel 'number' yang tidak ada sudah dihapus dari compact()
        return view('admin.exams.peserta', compact('exam', 'users', 'assignedUserIds', 'number_paginate'));
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
                    ->latest()
                    ->paginate(10);

        return view('admin.exams.results_index', compact('sessions'));
    }

    public function destroyResultsIndex($id)
    {
        // Ambil semua sesi yang sudah selesai atau sedang berlangsung
        $sessions = \App\ExamSession::findOrFail($id);
        $sessions->delete();

        return redirect()->route('manage-exams.results')->with('success', 'Hasil ujian berhasil dihapus.');
    }

    public function resultsShow($session_id)
    {
        $session = \App\ExamSession::with(['user', 'exam.questions', 'proctoringLogs', 'userAnswers'])
                    ->findOrFail($session_id);

        $verifyUser = VerifyUser::where('email', $session->user->email)->first();

        // Di sini nanti kamu bisa tambahkan logika hitung skor DISC/MBTI
        // berdasarkan data dari $session->answers

        return view('admin.exams.results_show', compact('session', 'verifyUser'));
    }

    // Menghasilkan dan mengembalikan QR code PNG berisi link publik ujian
    public function downloadQr($id)
    {
        $exam = Exam::findOrFail($id);

        // Gunakan link publik untuk peserta (route model binding by token)
        $publicLink = route('exam.show', $exam);

        // Gunakan QuickChart API untuk membuat QR sederhana tanpa dependensi tambahan
        $qrUrl = 'https://quickchart.io/qr?text=' . urlencode($publicLink) . '&size=400';

        try {
            $response = Http::get($qrUrl);
            if ($response->successful()) {
                $contents = $response->body();
                $filename = 'exam_qr_' . $exam->id . '.png';

                return response($contents, 200)
                    ->header('Content-Type', 'image/png')
                    ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
            }
        } catch (\Exception $e) {
            // Jika gagal, redirect kembali dengan pesan error
            return redirect()->back()->with('error', 'Gagal membuat QR: ' . $e->getMessage());
        }

        return redirect()->back()->with('error', 'Gagal membuat QR untuk ujian ini.');
    }

    public function destroy($id){
        $exam = Exam::findOrFail($id);
        $exam->delete();
    
        return redirect()->route('manage-exams.index')->with('success', 'Ujian berhasil dihapus.');
    }

    // ========================================================
    // FUNGSI EXPORT HASIL KE EXCEL (CSV FORMAT)
    // ========================================================
    // public function exportExcel($session_id)
    // {
    //     $session = \App\ExamSession::with(['user', 'exam.questions', 'userAnswers'])
    //                 ->findOrFail($session_id);
                    
    //     // Mengambil data murni dari hasil Verifikasi User
    //     $verifyUser = \App\VerifyUser::where('email', $session->user->email)->first();

    //     // Variabel untuk menampung data Verify User (Fallback jika kosong)
    //     $vName  = $verifyUser ? $verifyUser->name : $session->user->name;
    //     $vEmail = $verifyUser ? $verifyUser->email : $session->user->email;
    //     $vPhone = $verifyUser ? $verifyUser->phone : ($session->user->phone ?? '-');
    //     $vPos   = $verifyUser ? $verifyUser->position : ($session->user->position ?? '-');

    //     // Format Nama File (Contoh: Hasil_Ujian_Albert_20260605.csv)
    //     $fileName = 'Hasil_Ujian_' . preg_replace('/[^A-Za-z0-9]/', '_', $vName) . '_' . date('Ymd') . '.csv';

    //     $headers = [
    //         "Content-type"        => "text/csv",
    //         "Content-Disposition" => "attachment; filename=$fileName",
    //         "Pragma"              => "no-cache",
    //         "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
    //         "Expires"             => "0"
    //     ];

    //     $callback = function() use($session, $vName, $vEmail, $vPhone, $vPos) {
    //         $file = fopen('php://output', 'w');
            
    //         // Tambahkan BOM agar Microsoft Excel mendeteksi teks ini sebagai UTF-8 secara otomatis
    //         fputs($file, $bom =( chr(0xEF) . chr(0xBB) . chr(0xBF) ));

    //         // --- 1. TULIS HEADER DATA PESERTA ---
    //         fputcsv($file, ['=======================================']);
    //         fputcsv($file, ['INFO PESERTA (DARI DATA VERIFIKASI)']);
    //         fputcsv($file, ['=======================================']);
    //         fputcsv($file, ['Nama', $vName]);
    //         fputcsv($file, ['Email', $vEmail]);
    //         fputcsv($file, ['Telepon', $vPhone]);
    //         fputcsv($file, ['Posisi Dilamar', $vPos]);
    //         fputcsv($file, ['']); // Baris kosong pembatas
            
    //         // --- 2. TULIS HEADER DATA UJIAN ---
    //         fputcsv($file, ['=======================================']);
    //         fputcsv($file, ['INFO UJIAN']);
    //         fputcsv($file, ['=======================================']);
    //         fputcsv($file, ['Nama Ujian', $session->exam->name]);
    //         fputcsv($file, ['Tipe Ujian', strtoupper($session->exam->type)]);
    //         fputcsv($file, ['Status', strtoupper($session->status)]);
    //         fputcsv($file, ['Waktu Selesai', $session->end_time ?? '-']);
    //         fputcsv($file, ['']); // Baris kosong pembatas

    //         // --- 3. TULIS DETAIL JAWABAN PESERTA ---
    //         fputcsv($file, ['=======================================']);
    //         fputcsv($file, ['DETAIL JAWABAN SOAL']);
    //         fputcsv($file, ['=======================================']);
    //         fputcsv($file, ['No', 'Pertanyaan / Pernyataan', 'Jawaban Peserta']); // Header Tabel

    //         foreach ($session->exam->questions as $question) {
    //             $userAnswer = $session->userAnswers->where('question_number', $question->number)->first();
    //             $data = $userAnswer ? $userAnswer->answers : null;
    //             $answerString = '❌ Kosong / Tidak Terisi';

    //             if ($data) {
    //                 if ($session->exam->type == 'disc') {
    //                     $most = $data['most'] ?? '-';
    //                     $least = $data['least'] ?? '-';
    //                     $answerString = "Most: $most | Least: $least";
    //                 } elseif ($session->exam->type == 'angka' || $session->exam->type == 'uraian') {
    //                     // Jika memiliki struktur tabel kotak-kotak detail
    //                     if (isset($data['details']) && !empty($data['details'])) {
    //                         $ansArr = [];
    //                         foreach ($data['details'] as $lbl => $val) {
    //                             // Titik format angka
    //                             $valFormat = is_numeric($val) ? number_format($val, 0, ',', '.') : $val;
    //                             $ansArr[] = "$lbl : $valFormat";
    //                         }
    //                         $answerString = implode(" \n ", $ansArr); // Pisahkan dengan Enter di Excel
    //                     } else {
    //                         $answerString = $data['answer_text'] ?? '-';
    //                     }
    //                 } elseif ($session->exam->type == 'kasus_akuntansi') {
    //                     $uploadedFiles = json_decode($session->answer_file, true) ?: [];
    //                     if(!empty($uploadedFiles)){
    //                         $names = array_column($uploadedFiles, 'name');
    //                         $answerString = "File Diupload: " . implode(", ", $names);
    //                     } else {
    //                         $answerString = "❌ Tidak mengunggah file.";
    //                     }
    //                 } else {
    //                     // Untuk PG Akuntansi, MBTI, VAK
    //                     $answerString = $data['selected'] ?? '-';
    //                 }
    //             }

    //             // Hapus kode HTML/JSON panjang dari soal agar Excel tidak rusak
    //             $qText = $session->exam->type == 'angka' ? 'Soal Penjumlahan Tabel Angka' : strip_tags($question->question_text);
                
    //             fputcsv($file, [$question->number, $qText, $answerString]);
    //         }

    //         fclose($file);
    //     };

    //     return response()->stream($callback, 200, $headers);
    // }

    // ========================================================
    // FUNGSI EXPORT HASIL KE EXCEL (VERSI RAPIH & STYLING)
    // ========================================================
    public function exportExcel($session_id)
    {
        $session = \App\ExamSession::with(['user', 'exam.questions', 'userAnswers'])
                    ->findOrFail($session_id);
                    
        $verifyUser = \App\VerifyUser::where('email', $session->user->email)->first();

        $vName  = $verifyUser ? $verifyUser->name : $session->user->name;
        $vEmail = $verifyUser ? $verifyUser->email : $session->user->email;
        $vPhone = $verifyUser ? $verifyUser->phone : ($session->user->phone ?? '-');
        $vPos   = $verifyUser ? $verifyUser->position : ($session->user->position ?? '-');

        // Menggunakan ekstensi .xls agar format styling HTML (warna/garis) terbaca sempurna di Excel
        $fileName = 'Hasil_Ujian_' . preg_replace('/[^A-Za-z0-9]/', '_', $vName) . '_' . date('Ymd') . '.xls';

        // Lempar data ke file Blade baru yang akan kita buat
        return response(view('admin.exams.export_excel', compact('session', 'vName', 'vEmail', 'vPhone', 'vPos')))
            ->header('Content-Type', 'application/vnd.ms-excel')
            ->header('Content-Disposition', 'attachment; filename="' . $fileName . '"');
    }
}


