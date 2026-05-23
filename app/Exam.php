<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exam extends Model
{
    //
    protected $guarded = ['id'];

    protected static function booted()
    {
        static::creating(function ($exam) {
            if (empty($exam->public_token)) {
                $exam->public_token = Str::random(32);
            }
        });
    }

    public function getRouteKeyName()
    {
        return 'public_token';
    }

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

    public function questions()
    {
        return $this->hasMany(Question::class, 'exam_id');
    }

}
