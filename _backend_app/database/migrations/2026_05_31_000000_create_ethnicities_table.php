<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('ethnicities', function (Blueprint $table) {
            $table->id();
            $table->string('eth_en', 50);
            $table->string('eth_kh', 50);
            $table->string('eth_label', 50);
        });

        $items = [
            [
                'eth_en' => 'Khmer',
                'eth_kh' => 'ខ្មែរ',
                'eth_label' => 'KH',
            ],
            [
                'eth_en' => 'Vietnamese',
                'eth_kh' => 'វៀតណាម',
                'eth_label' => 'VN',
            ],
            [
                'eth_en' => 'Lao',
                'eth_kh' => 'ឡាវ',
                'eth_label' => 'LA',
            ]
        ];
        foreach ($items as $item) {
            DB::table('ethnicities')->insert($item);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ethnicities');
    }
};
