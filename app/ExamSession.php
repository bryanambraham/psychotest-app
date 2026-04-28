<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;
use App\Exam;
use App\UserAnswer;


class ExamSession extends Model
{
    //
    protected $guarded = ['id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function userAnswers()
    {
        return $this->hasMany(UserAnswer::class);
    }
}
