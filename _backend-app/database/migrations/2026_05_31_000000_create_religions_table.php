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
        Schema::create('religions', function (Blueprint $table) {
            $table->id();
            $table->string('rel_en', 50);
            $table->string('rel_kh', 50);
            $table->string('rel_label', 50);
        });

        $items = [
            [
                'rel_en' => 'Buddhism',
                'rel_kh' => 'សាសនា​ព្រះពុទ្ធ',
                'rel_label' => 'Buddhism',
            ],
            [
                'rel_en' => 'Christianity',
                'rel_kh' => 'សាសនា​គ្រិស្ត',
                'rel_label' => 'Christianity',
            ],
            [
                'rel_en' => 'Hinduism',
                'rel_kh' => 'សាសនា​ហិណ្ឌូ',
                'rel_label' => 'Hinduism',
            ],
        ];
        foreach ($items as $item) {
            DB::table('religions')->insert($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('religions');
    }
};
