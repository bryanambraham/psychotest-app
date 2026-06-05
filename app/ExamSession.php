<?php

namespace App;

use Illuminate\Database\Eloquent\Model;


class ExamSession extends Model
{
    //
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class, 'exam_session_id');
    }

    public function proctoringLogs()
    {
        return $this->hasMany(ProctoringLog::class, 'exam_session_id');
    }

    /**
     * Hitung dan simpan score berdasarkan kunci jawaban
     * Formula: (jumlah benar * 100) / jumlah soal
     */
    public function calculateAndSaveScore()
    {
        $session = ExamSession::find($this->id);
        $exam = $session->exam()->with('questions')->first();
        
        if (!$exam) return null;
        
        $questions = $exam->questions;
        $questionsWithAnswerKey = $questions->filter(function($q) {
            return $q->answer_key !== null && $q->answer_key !== '';
        });

        if ($questionsWithAnswerKey->isEmpty()) return null;

        // KITA UBAH LOGIKANYA MENJADI SISTEM POIN TOTAL
        $totalPossiblePoints = 0;
        $totalEarnedPoints = 0;

        foreach ($questionsWithAnswerKey as $question) {
            $userAnswer = $session->userAnswers()->where('question_number', $question->number)->first();

            $userAnswerData = [];
            if ($userAnswer && $userAnswer->answers) {
                $userAnswerData = is_array($userAnswer->answers) ? $userAnswer->answers : json_decode($userAnswer->answers, true);
            }

            // ==========================================
            // LOGIKA KHUSUS SOAL ANGKA (NILAI PER KOTAK)
            // ==========================================
            if ($exam->type === 'angka' || isset($userAnswerData['details'])) {
                $answerKeyData = @json_decode($question->answer_key, true);
                
                if (is_array($answerKeyData) && !empty($answerKeyData)) {
                    // Tambahkan jumlah KOTAK JAWABAN sebagai target Poin Maksimal
                    $totalPossiblePoints += count($answerKeyData);

                    foreach ($answerKeyData as $label => $expectedValue) {
                        $actualValue = $userAnswerData['details'][$label] ?? null;
                        
                        if ($actualValue !== null) {
                            $normalizedActual = str_replace(['.', ','], '', strtolower(trim($actualValue)));
                            $normalizedExpected = str_replace(['.', ','], '', strtolower(trim($expectedValue)));
                            
                            if ($normalizedActual === $normalizedExpected) {
                                $totalEarnedPoints++; // Tambah 1 Poin setiap 1 Kotak yang Benar!
                            }
                        }
                    }
                }
            }
            // ==========================================
            // LOGIKA SOAL STANDAR (PG/URAIAN)
            // ==========================================
            else {
                $totalPossiblePoints += 1; // Soal biasa targetnya hanya 1 poin

                if (!empty($userAnswerData)) {
                    $isCorrect = false;

                    if (isset($userAnswerData['selected'])) {
                        $userAnswerValue = trim($userAnswerData['selected']);
                        $expectedValue = trim($question->answer_key ?? '');
                        if ($expectedValue && strtolower($userAnswerValue) === strtolower($expectedValue)) $isCorrect = true;
                    } 
                    else if (isset($userAnswerData['answer_text'])) {
                        $userAnswerValue = trim($userAnswerData['answer_text']);
                        $expectedValue = trim($question->answer_key ?? '');
                        if ($expectedValue && strtolower($userAnswerValue) === strtolower($expectedValue)) $isCorrect = true;
                    }

                    if ($isCorrect) $totalEarnedPoints++;
                }
            }
        }

        // Kalkulasi Skor Akhir: (Total Poin Didapat / Total Poin Maksimal) x 100
        $score = $totalPossiblePoints > 0 ? ($totalEarnedPoints * 100) / $totalPossiblePoints : 0;
        
        $this->update(['score' => $score]);
        return $score;
    }
}
