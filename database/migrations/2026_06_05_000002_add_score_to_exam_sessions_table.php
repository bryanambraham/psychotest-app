<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddScoreToExamSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->decimal('score', 5, 2)->nullable()->after('status')->comment('Nilai akhir (0-100)');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('exam_sessions', function (Blueprint $table) {
            $table->dropColumn('score');
        });
    }
}
