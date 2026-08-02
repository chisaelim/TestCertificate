<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('student_tests', function (Blueprint $table) {
            $table->foreign(['student_id'], 'student_tests_ibfk_1')->references(['id'])->on('students')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['test_id'], 'student_tests_ibfk_2')->references(['id'])->on('tests')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['created_by'], 'student_tests_ibfk_3')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['updated_by'], 'student_tests_ibfk_4')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('student_tests', function (Blueprint $table) {
            $table->dropForeign('student_tests_ibfk_1');
            $table->dropForeign('student_tests_ibfk_2');
            $table->dropForeign('student_tests_ibfk_3');
            $table->dropForeign('student_tests_ibfk_4');
        });
    }
};
