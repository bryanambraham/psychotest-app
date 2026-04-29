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
