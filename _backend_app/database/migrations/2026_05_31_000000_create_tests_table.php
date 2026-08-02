<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tests', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 250);
            $table->string('name_kh', 250);
            $table->string('short_name', 250);
            $table->unsignedBigInteger('created_by')->index('created_by');
            $table->unsignedBigInteger('updated_by')->index('updated_by');
            $table->timestamps();
        });

        $tests = [
            [
                "id" => 1,
                "name_en" => "Arc Welding",
                "name_kh" => "ការផ្សារអគ្គិសនី",
                "short_name" => "Arc Welding",
                "created_by" => 1,
                "updated_by" => 1
            ],
            [
                "id" => 2,
                "name_en" => "Electrical Welding",
                "name_kh" => "ការដំឡើងបណ្តាញអគ្គិសនីក្នុងអគារ",
                "short_name" => "Electrical Welding",
                "created_by" => 1,
                "updated_by" => 1
            ],
            [
                "id" => 3,
                "name_en" => "Food Processing",
                "name_kh" => "ការកែច្នៃអាហារ និងផ្លែឈើ",
                "short_name" => "Food Processing",
                "created_by" => 1,
                "updated_by" => 1
            ],
            [
                "id" => 4,
                "name_en" => "Form Work",
                "name_kh" => "ការដំឡើងពុម្ព",
                "short_name" => "Form Work",
                "created_by" => 1,
                "updated_by" => 1
            ],
            [
                "id" => 5,
                "name_en" => "Masonry",
                "name_kh" => "ការងារកំបោរ",
                "short_name" => "Masonry",
                "created_by" => 1,
                "updated_by" => 1
            ],
            [
                "id" => 6,
                "name_en" => "Plumbing",
                "name_kh" => "ការដំឡើងបណ្តាញទឹកក្នុងអគារ",
                "short_name" => "Plumbing",
                "created_by" => 1,
                "updated_by" => 1
            ]
        ];

        $time = Carbon\Carbon::now()->toDateTimeString();
        foreach ($tests as $test) {
            DB::table('tests')->insert([
                ...$test,
                'created_at' => $time,
                'updated_at' => $time,
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tests');
    }
};
