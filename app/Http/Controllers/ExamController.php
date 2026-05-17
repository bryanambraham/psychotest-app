<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exam;
use App\ExamSession;
use App\UserAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ExamController extends Controller
{
    public function show($exam_id)
    {
        $user = auth()->user();

        // CEK AKSES: Apakah ID ujian ini ada di daftar penugasan user?
        $hasAccess = $user->exams()->where('exam_id', $exam_id)->exists();

        if (!$hasAccess) {
            return redirect('/home')->with('error', 'Maaf, Anda tidak memiliki akses untuk ujian ini.');
        }

        // Mencari data ujian, jika tidak ada akan error 404
        // Ambil ujian beserta soal-soalnya
        $exam = Exam::with('questions')->findOrFail($exam_id);


        $session = ExamSession::firstOrCreate(
            [
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'status'  => 'in_progress'
            ],
            [
                'start_time' => Carbon::now()
            ]
        );

        $endTime = Carbon::parse($session->start_time)->addMinutes($exam->duration_minutes);
        $remainingSeconds = Carbon::now()->diffInSeconds($endTime, false);

        if ($remainingSeconds <= 0) {
            $session->update(['status' => 'timeout']);
            return redirect('/home')->with('error', 'Waktu ujian Anda sudah habis.');
        }

        return view('exam.show', compact('exam', 'session', 'remainingSeconds'));
    }

    public function storeAnswer(Request $request)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'question_number' => 'required|integer',
            'answers'         => 'required|array'
        ]);

        UserAnswer::updateOrCreate(
            [
                'exam_session_id' => $request->exam_session_id,
                'question_number' => $request->question_number
            ],
            [
                'answers' => $request->answers
            ]
        );

        return response()->json(['status' => 'success']);
    }


    public function uploadFileAnswer(Request $request)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'answer_files'    => 'required|array', // Validasi input harus berupa array file
            'answer_files.*'  => 'required|mimes:xlsx,xls,pdf,doc,docx|max:10000', // Maks 10MB per file
        ]);

        $session = ExamSession::findOrFail($request->exam_session_id);

        // Ambil daftar berkas lama di DB agar tidak terhapus saat peserta mencicil upload berkas baru
        $currentFiles = json_decode($session->answer_file, true) ?: [];

        $relativeFolder = 'uploads/answers/' . $session->id;
        $destinationPath = public_path($relativeFolder);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        if ($request->hasFile('answer_files')) {
            foreach ($request->file('answer_files') as $file) {
                // Berikan token uniqid agar nama file tidak bentrok jika mengunggah file bernama sama
                $filename = 'answer_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);

                $dbPath = $relativeFolder . '/' . $filename;

                // Masukkan data struktur file ke dalam list
                $currentFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $dbPath
                ];
            }
        }

        // Update kolom dengan bentuk JSON Array stringified
        $session->update([
            'answer_file' => json_encode($currentFiles)
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Berkas berhasil disimpan!',
            'files' => $currentFiles
        ]);
    }

    public function deleteFileAnswer(Request $request)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'file_path'       => 'required|string', // Path file relatif yang mau didelete
        ]);

        $session = ExamSession::findOrFail($request->exam_session_id);

        // Ambil daftar file JSON saat ini
        $currentFiles = json_decode($session->answer_file, true) ?: [];

        $updatedFiles = [];
        $fileDeleted = false;

        foreach ($currentFiles as $file) {
            // Jika path-nya cocok, hapus fisik filenya dari folder public
            if ($file['path'] === $request->file_path) {
                $absolutePath = public_path($file['path']);
                if (File::exists($absolutePath)) {
                    File::delete($absolutePath);
                }
                $fileDeleted = true;
            } else {
                // Jika tidak cocok, amankan filenya ke dalam list update
                $updatedFiles[] = $file;
            }
        }

        if ($fileDeleted) {
            // Update database dengan array yang baru (jika kosong jadikan null)
            $session->update([
                'answer_file' => empty($updatedFiles) ? null : json_encode(array_values($updatedFiles))
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berkas berhasil dibatalkan!',
            'files' => array_values($updatedFiles) // Kirim sisa file terbaru ke frontend
        ]);
    }

    public function finish($session_id)
    {
        $session = ExamSession::findOrFail($session_id);

        // Pastikan hanya pemilik sesi yang bisa mengakhiri
        if ($session->user_id !== auth()->id()) {
            abort(403);
        }

        // Update status dan catat waktu selesai
        $session->update([
            'status' => 'completed',
            'end_time' => now()
        ]);

        return redirect()->route('home')->with('success', 'Ujian telah berhasil dikumpulkan. Terima kasih!');
    }
}
