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
        Schema::create('student_tests', function (Blueprint $table) {
            $table->id();
            $table->string('issued_date', 10);
            $table->string('expired_date', 10);
            $table->enum('status', ['PENDING', 'PASSED', 'FAILED']);
            $table->unsignedBigInteger('student_id')->index('student_id');
            $table->unsignedBigInteger('test_id')->index('test_id');
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
        Schema::dropIfExists('student_tests');
    }
};
