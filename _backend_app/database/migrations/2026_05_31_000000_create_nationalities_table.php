<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('nationalities', function (Blueprint $table) {
            $table->id();
            $table->string('nat_en', 50);
            $table->string('nat_kh', 50);
            $table->string('nat_label', 50);
        });

        $items = [
            [
                'nat_en' => 'Khmer',
                'nat_kh' => 'ខ្មែរ',
                'nat_label' => 'KH',
            ],
            [
                'nat_en' => 'Vietnamese',
                'nat_kh' => 'វៀតណាម',
                'nat_label' => 'VN',
            ],
            [
                'nat_en' => 'Lao',
                'nat_kh' => 'ឡាវ',
                'nat_label' => 'LA',
            ],
        ];
        foreach ($items as $item) {
            DB::table('nationalities')->insert($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nationalities');
    }
};
