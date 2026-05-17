<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAnswerFileToExamSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->text('answer_file')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn('answer_file');
        });
    }
}
