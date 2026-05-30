<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('genders', function (Blueprint $table) {
            $table->id();
            $table->string('gd_en', 25)->unique('gd_en');
            $table->string('gd_en_full', 25)->unique('gd_en_full');
            $table->string('gd_kh', 25)->unique('gd_kh');
            $table->string('gd_kh_full', 25)->unique('gd_kh_full');
        });

        $items = [
            [
                'gd_en' => 'M',
                'gd_kh' => 'ប',
                'gd_en_full' => 'Male',
                'gd_kh_full' => 'ប្រុស',
            ],
            [
                'gd_en' => 'F',
                'gd_kh' => 'ស',
                'gd_en_full' => 'Female',
                'gd_kh_full' => 'ស្រី',
            ]
        ];
        foreach ($items as $item) {
            DB::table('genders')->insert($item);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('genders');
    }
};
