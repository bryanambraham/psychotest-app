<?php

namespace App\Console\Commands;

use App\Question;
use Illuminate\Console\Command;

class CheckAnswerKeys extends Command
{
    protected $signature = 'check:answer-keys {exam_id}';
    protected $description = 'Check answer keys for an exam';

    public function handle()
    {
        $examId = $this->argument('exam_id');
        $questions = Question::where('exam_id', $examId)->get();

        $this->table(
            ['No Soal', 'Answer Key', 'Type'],
            $questions->map(function($q) {
                $isJson = json_decode($q->answer_key) !== null && is_array(json_decode($q->answer_key, true));
                return [
                    $q->number,
                    strlen($q->answer_key) > 100 ? substr($q->answer_key, 0, 100) . '...' : $q->answer_key,
                    $isJson ? 'JSON' : 'STRING'
                ];
            })->toArray()
        );
    }
}
