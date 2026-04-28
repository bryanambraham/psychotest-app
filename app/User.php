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


}
