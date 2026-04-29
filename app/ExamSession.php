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


}
