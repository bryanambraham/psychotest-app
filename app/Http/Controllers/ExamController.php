<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exam;
use App\ExamSession;
use App\UserAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ExamController extends Controller
{
    // Menampilkan halaman ujian beserta sisa waktu yang aman
    public function show($exam_id)
    {
        $exam = Exam::findOrFail($exam_id);

        // Cari sesi yang sedang berjalan, atau buat baru jika belum mulai
        $session = ExamSession::firstOrCreate(
            [
                'user_id' => Auth::id(),
                'exam_id' => $exam->id,
                'status' => 'in_progress'
            ],
            [
                'start_time' => Carbon::now()
            ]
        );

        // Hitung sisa waktu mundur (berdasarkan start_time di database, bukan di JS)
        $endTime = Carbon::parse($session->start_time)->addMinutes($exam->duration_minutes);
        $remainingSeconds = Carbon::now()->diffInSeconds($endTime, false);

        // Jika waktu sudah minus (habis)
        if ($remainingSeconds <= 0) {
            $session->update(['status' => 'timeout']);
            return redirect('/home')->with('error', 'Waktu ujian Anda sudah habis.');
        }

        // Kirim sisa waktu (dalam detik) ke Frontend/Blade
        return view('exam.show', compact('exam', 'session', 'remainingSeconds'));
    }

    // Endpoint untuk menyimpan jawaban (Bisa ditembak via AJAX/Axios)
    public function storeAnswer(Request $request)
    {
        $request->validate([
            'exam_session_id' => 'required|exists:exam_sessions,id',
            'question_number' => 'required|integer',
            'answers'         => 'required|array' // Pastikan frontend mengirim bentuk array/object JS
        ]);

        // Gunakan updateOrCreate agar user bisa merevisi jawabannya
        UserAnswer::updateOrCreate(
            [
                'exam_session_id' => $request->exam_session_id,
                'question_number' => $request->question_number
            ],
            [
                'answers' => $request->answers // Laravel otomatis mengubah array ini jadi JSON di DB
            ]
        );

        return response()->json(['status' => 'success', 'message' => 'Jawaban tersimpan otomatis.']);
    }
}
