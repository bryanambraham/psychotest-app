<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    //
    protected $guarded = ['id'];

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'user_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_user', 'user_id', 'exam_id')->withTimestamps();
    }
}
