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

}
