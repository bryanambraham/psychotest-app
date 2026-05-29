<?php

namespace App\Http\Controllers;

use App\Exam;
use App\ExamSession;
use App\User;
use App\UserAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ExamController extends Controller
{
    public function show(Exam $exam)
    {
        $activeSessionId = session()->get('active_exam_session.' . $exam->id);

        if ($activeSessionId) {
            $session = ExamSession::find($activeSessionId);

            if ($session && $session->status === 'in_progress') {
                return redirect()->route('exam.take', [
                    'exam' => $exam,
                    'session_id' => $session->id,
                ]);
            }
        }

        $participant = session()->get('exam_candidate.' . $exam->id);

        return view('exam.start', compact('exam', 'participant'));
    }

    public function storeParticipant(Request $request, Exam $exam)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'position' => 'required|string|max:50',
            'phone' => 'required|string|max:30',
            'email' => 'required|string|email|max:255',
        ]);

        session()->put('exam_candidate.' . $exam->id, [
            'name'     => trim(strtolower($request->name)),
            'position' => trim(strtolower($request->position)),
            'phone'    => trim(strtolower($request->phone)),
            'email'    => trim(strtolower($request->email)),
        ]);

        return redirect()->route('exam.instructions', $exam);
    }

    public function instructions(Exam $exam)
    {
        $participant = session()->get('exam_candidate.' . $exam->id);

        if (!$participant) {
            return redirect()->route('exam.show', $exam)
                ->with('error', 'Silakan isi data peserta terlebih dahulu.');
        }

        return view('exam.instructions', compact('exam', 'participant'));
    }

    public function begin(Request $request, Exam $exam)
    {
        $exam->load('questions');
        $participant = session()->get('exam_candidate.' . $exam->id);

        if (!$participant) {
            return redirect()->route('exam.show', $exam)
                ->with('error', 'Silakan isi data peserta terlebih dahulu.');
        }

        $user = User::where('email', $participant['email'])->first();

        if ($user) {
            $existingSession = ExamSession::where('user_id', $user->id)
                ->where('exam_id', $exam->id)
                ->first();

            if ($existingSession) {
                if ($existingSession->status === 'in_progress') {
                    session()->put('active_exam_session.' . $exam->id, $existingSession->id);

                    return redirect()->route('exam.take', [
                        'exam' => $exam,
                        'session_id' => $existingSession->id,
                    ]);
                }

                return redirect()->route('exam.instructions', $exam)
                    ->with('error', 'Email ini sudah pernah dipakai untuk mengerjakan ujian ini. Satu email hanya bisa mengerjakan satu kali.');
            }
        }

        if (!$user) {
            $user = User::create([
                'name'     => $participant['name'],
                'email'    => $participant['email'],
                'position' => $participant['position'],
                'phone'    => $participant['phone'],
                'password' => Hash::make(Str::random(40)),
                'role'     => 'user',
            ]);
        } elseif ($user->role === 'user') {
            $user->update([
                'name'     => $participant['name'],
                'position' => $participant['position'],
                'phone'    => $participant['phone'],
            ]);
        }

        $session = ExamSession::firstOrCreate(
            [
                'user_id' => $user->id,
                'exam_id' => $exam->id,
                'status'  => 'in_progress',
            ],
            [
                'start_time' => Carbon::now(),
            ]
        );

        if (!$session->start_time) {
            $session->update(['start_time' => Carbon::now()]);
        }

        session()->put('active_exam_session.' . $exam->id, $session->id);
        session()->forget('exam_candidate.' . $exam->id);

        return redirect()->route('exam.take', [
            'exam' => $exam,
            'session_id' => $session->id,
        ]);
    }

    public function take(Exam $exam, $session_id)
    {
        $session = ExamSession::with(['user', 'exam.questions'])->findOrFail($session_id);

        if ((int) $session->exam_id !== (int) $exam->id) {
            abort(404);
        }

        $activeSessionId = session()->get('active_exam_session.' . $exam->id);
        if ((int) $activeSessionId !== (int) $session->id) {
            return redirect()->route('exam.show', $exam)
                ->with('error', 'Silakan mulai ujian dari halaman awal terlebih dahulu.');
        }

        $endTime = Carbon::parse($session->start_time)->addMinutes($exam->duration_minutes);
        $remainingSeconds = Carbon::now()->diffInSeconds($endTime, false);

        if ($remainingSeconds <= 0) {
            $session->update(['status' => 'timeout']);
            session()->forget('active_exam_session.' . $exam->id);

            return view('exam.completed', [
                'exam' => $exam,
                'session' => $session,
            ]);
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
            'answer_files'    => 'required|array',
            'answer_files.*'  => 'required|mimes:xlsx,xls,pdf,doc,docx|max:10000',
        ]);

        $session = ExamSession::findOrFail($request->exam_session_id);

        $currentFiles = json_decode($session->answer_file, true) ?: [];

        $relativeFolder = 'uploads/answers/' . $session->id;
        $destinationPath = public_path($relativeFolder);

        if (!File::exists($destinationPath)) {
            File::makeDirectory($destinationPath, 0755, true);
        }

        if ($request->hasFile('answer_files')) {
            foreach ($request->file('answer_files') as $file) {
                $filename = 'answer_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move($destinationPath, $filename);

                $dbPath = $relativeFolder . '/' . $filename;

                $currentFiles[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $dbPath
                ];
            }
        }

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
            'file_path'       => 'required|string',
        ]);

        $session = ExamSession::findOrFail($request->exam_session_id);

        $currentFiles = json_decode($session->answer_file, true) ?: [];

        $updatedFiles = [];
        $fileDeleted = false;

        foreach ($currentFiles as $file) {
            if ($file['path'] === $request->file_path) {
                $absolutePath = public_path($file['path']);
                if (File::exists($absolutePath)) {
                    File::delete($absolutePath);
                }
                $fileDeleted = true;
            } else {
                $updatedFiles[] = $file;
            }
        }

        if ($fileDeleted) {
            $session->update([
                'answer_file' => empty($updatedFiles) ? null : json_encode(array_values($updatedFiles))
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Berkas berhasil dibatalkan!',
            'files' => array_values($updatedFiles)
        ]);
    }

    public function finish($session_id)
    {
        $session = ExamSession::with('exam')->findOrFail($session_id);

        $activeSessionId = session()->get('active_exam_session.' . $session->exam_id);
        if ((int) $activeSessionId !== (int) $session->id) {
            abort(403);
        }

        $session->update([
            'status' => 'completed',
            'end_time' => now()
        ]);

        session()->forget('active_exam_session.' . $session->exam_id);

        return view('exam.completed', [
            'exam' => $session->exam,
            'session' => $session,
        ]);
    }
}
