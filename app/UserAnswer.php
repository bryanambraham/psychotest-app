<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\ExamSession;

class UserAnswer extends Model
{
    protected $guarded = ['id'];

    // Ini yang akan otomatis mengubah JSON dari DB menjadi Array PHP
    protected $casts = [
        'answers' => 'array',
    ];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }
}
