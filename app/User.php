<?php

namespace App;

use App\Mail\UserCreatedMail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Mail;

class User extends Authenticatable
{
    protected $guarded = ['id'];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            Mail::to($user->email)->send(new UserCreatedMail($user));
        });
    }

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'user_id');
    }

    public function exams()
    {
        return $this->belongsToMany(Exam::class, 'exam_user', 'user_id', 'exam_id')->withTimestamps();
    }
}
