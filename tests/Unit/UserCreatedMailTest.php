<?php

namespace Tests\Unit;

use App\User;
use App\Mail\UserCreatedMail;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserCreatedMailTest extends TestCase
{
    /** @test */
    public function it_sends_an_email_when_user_is_created()
    {
        Mail::fake();

        User::create([
            'name' => 'alice',
            'email' => 'alice@example.com',
            'password' => 'secret123',
        ]);

        Mail::assertSent(UserCreatedMail::class, function ($mail) {
            return $mail->hasTo('alice@example.com');
        });
    }
}
