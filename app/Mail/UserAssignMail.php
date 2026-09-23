<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserAssignMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $exam;

    public function __construct($user, $exam)
    {
        $this->user = $user;
        $this->exam = $exam;
    }

    public function build()
    {
        return $this->subject('Akun Anda telah diassign untuk mengikuti ujian.')
            ->markdown('emails.user-assign')
            ->with([
                'user' => $this->user,
                'exam' => $this->exam,
            ]);
    }
}
