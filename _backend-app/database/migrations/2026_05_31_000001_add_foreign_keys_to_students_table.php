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
        Schema::table('students', function (Blueprint $table) {
            $table->foreign(['gender_id'], 'students_ibfk_1')->references(['id'])->on('genders')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['nationality_id'], 'students_ibfk_2')->references(['id'])->on('nationalities')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['ethnicity_id'], 'students_ibfk_3')->references(['id'])->on('ethnicities')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['religion_id'], 'students_ibfk_4')->references(['id'])->on('religions')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['place_of_residence_id'], 'students_ibfk_5')->references(['id'])->on('geographies')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['place_of_birth_id'], 'students_ibfk_6')->references(['id'])->on('geographies')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['created_by'], 'students_ibfk_7')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['updated_by'], 'students_ibfk_8')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropForeign('students_ibfk_1');
            $table->dropForeign('students_ibfk_2');
            $table->dropForeign('students_ibfk_3');
            $table->dropForeign('students_ibfk_4');
            $table->dropForeign('students_ibfk_5');
            $table->dropForeign('students_ibfk_6');
            $table->dropForeign('students_ibfk_7');
            $table->dropForeign('students_ibfk_8');
        });
    }
};
