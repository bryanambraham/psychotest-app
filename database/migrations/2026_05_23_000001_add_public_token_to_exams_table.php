<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AddPublicTokenToExamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->string('public_token', 64)->nullable()->unique()->after('id');
        });

        \App\Exam::whereNull('public_token')->chunkById(100, function ($exams) {
            foreach ($exams as $exam) {
                do {
                    $token = Str::random(32);
                } while (\App\Exam::where('public_token', $token)->exists());

                $exam->update(['public_token' => $token]);
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exams', function (Blueprint $table) {
            $table->dropUnique('exams_public_token_unique');
            $table->dropColumn('public_token');
        });
    }
}
