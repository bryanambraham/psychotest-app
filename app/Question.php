<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    //
    protected $guarded = ['id'];

    public function exam()
    {
        return $this->belongsTo(Exam::class, 'exam_id');
    }

    protected $casts = ['options' => 'array'];
}
