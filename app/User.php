<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    protected $guarded = ['id'];

    public function examSessions()
    {
        return $this->hasMany(ExamSession::class, 'user_id');
    }


}
