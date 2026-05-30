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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 250);
            $table->string('name_kh', 250);
            $table->string('dob', 10);
            $table->string('home_no', 25)->nullable();
            $table->string('street_no', 25)->nullable();
            $table->string('phone', 10)->nullable();
            $table->string('photo', 250)->nullable();
            $table->unsignedBigInteger('gender_id')->index('gender_id');
            $table->unsignedBigInteger('nationality_id')->index('nationality_id');
            $table->unsignedBigInteger('ethnicity_id')->index('ethnicity_id');
            $table->unsignedBigInteger('religion_id')->index('religion_id');
            $table->unsignedBigInteger('place_of_residence_id')->index('place_of_residence_id');
            $table->unsignedBigInteger('place_of_birth_id')->index('place_of_birth_id');
            $table->unsignedBigInteger('created_by')->index('created_by');
            $table->unsignedBigInteger('updated_by')->index('updated_by');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
