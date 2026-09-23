<?php

namespace Tests\Unit;

use App\Exam;
use App\Http\Controllers\ExamManagementController;
use App\Mail\UserAssignMail;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class UserAssignMailTest extends TestCase
{
    /** @test */
    public function it_renders_assignment_mail_for_participant_data_without_password()
    {
        $exam = Exam::create([
            'name' => 'Tes Psikotes',
            'type' => 'mbti',
            'duration_minutes' => 30,
            'description' => 'Deskripsi ujian',
        ]);

        $user = (object) [
            'name' => 'Alice',
            'email' => 'alice@example.com',
        ];

        $mail = new UserAssignMail($user, $exam);

        $html = $mail->render();

        $this->assertStringContainsString('Alice', $html);
        $this->assertStringContainsString('alice@example.com', $html);
        $this->assertStringContainsString('Tes Psikotes', $html);
    }

    /** @test */
    public function it_sends_assignment_mail_to_newly_assigned_users()
    {
        Mail::fake();

        $exam = Exam::create([
            'name' => 'Tes Psikotes',
            'type' => 'mbti',
            'duration_minutes' => 30,
            'description' => 'Deskripsi ujian',
        ]);

        $user = User::create([
            'name' => 'Bob',
            'email' => 'bob@example.com',
            'password' => 'secret123',
            'role' => 'user',
        ]);

        $controller = new ExamManagementController();
        $request = new Request([
            'name' => 'Tes Psikotes',
            'type' => 'mbti',
            'duration_minutes' => 30,
            'description' => 'Deskripsi ujian',
            'user_ids' => [$user->id],
            'visible_user_ids' => [],
        ]);

        $controller->update($request, $exam->id);

        Mail::assertSent(UserAssignMail::class, function ($mail) use ($user, $exam) {
            return $mail->hasTo($user->email)
                && $mail->exam->id === $exam->id;
        });
    }
}
