<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
{
    //
    protected $guarded = ['id'];

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'exam_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'exam_user', 'exam_id', 'user_id')->withTimestamps();
    }

    public function sessions()
    {
        return $this->hasMany(ExamSession::class, 'exam_id');
    }
}
