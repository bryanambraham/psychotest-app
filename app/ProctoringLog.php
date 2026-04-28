<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProctoringLog extends Model
{
    //
    protected $guarded = ['id'];

    public function examSession()
    {
        return $this->belongsTo(ExamSession::class, 'exam_session_id');
    }


}
