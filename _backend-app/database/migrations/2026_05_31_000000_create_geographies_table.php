<?php

use App\Models\Geography;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('geographies', function (Blueprint $table) {
            $table->id();
            $table->string('name_en', 250);
            $table->string('name_kh', 250);
            $table->string('name_latin', 250);
            $table->enum('unit_en', ['Village', 'Commune', 'Quarter', 'District', 'Section', 'Municipality', 'Province', 'Capital']);
            $table->enum('unit_kh', ['ភូមិ', 'ឃុំ', 'សង្កាត់', 'ស្រុក', 'ខណ្ឌ', 'ក្រុង', 'ខេត្ត', 'រាជធានី']);
            $table->enum('unit_latin', ['Phum', 'Khum', 'Sangkat', 'Srok', 'Khan', 'Khaet', 'Krong', 'Reach Theani']);
            $table->unsignedBigInteger('parent_id')->index('parent_id')->nullable();
        });

        $contents = File::get(base_path('/public/assets/jsons/provinces.json'));
        $provinces_json = json_decode(json: $contents);

        $contents = File::get(base_path('/public/assets/jsons/districts.json'));
        $districts_json = json_decode(json: $contents);

        $contents = File::get(base_path('/public/assets/jsons/communes.json'));
        $communes_json = json_decode(json: $contents);

        $contents = File::get(base_path('/public/assets/jsons/villages.json'));
        $villages_json = json_decode(json: $contents);

        try {
            DB::beginTransaction();
            foreach ($provinces_json->provinces as $key => $value) {
                DB::table('geographies')->insert([
                    'id' => intval($key),
                    'name_kh' => $value->name->km,
                    'name_en' => $value->name->latin,
                    'name_latin' => $value->name->latin,
                    'unit_kh' => $value->administrative_unit->km,
                    'unit_en' => $value->administrative_unit->en,
                    'unit_latin' => $value->administrative_unit->latin,
                    'parent_id' => null,
                ]);
            }
            foreach ($districts_json->districts as $key => $value) {
                DB::table('geographies')->insert([
                    'id' => intval($key),
                    'name_kh' => $value->name->km,
                    'name_en' => $value->name->latin,
                    'name_latin' => $value->name->latin,
                    'unit_kh' => $value->administrative_unit->km,
                    'unit_en' => $value->administrative_unit->en,
                    'unit_latin' => $value->administrative_unit->latin,
                    'parent_id' => intval(substr($key, 0, 2)),
                ]);
            }

            foreach ($communes_json->communes as $key => $value) {
                DB::table('geographies')->insert([
                    'id' => intval($key),
                    'name_kh' => $value->name->km,
                    'name_en' => $value->name->latin,
                    'name_latin' => $value->name->latin,
                    'unit_kh' => $value->administrative_unit->km,
                    'unit_en' => $value->administrative_unit->en,
                    'unit_latin' => $value->administrative_unit->latin,
                    'parent_id' => intval(substr($key, 0, 4)),
                ]);
            }

            foreach ($villages_json->villages as $key => $value) {
                DB::table('geographies')->insert([
                    'id' => intval($key),
                    'name_kh' => $value->name->km,
                    'name_en' => $value->name->latin,
                    'name_latin' => $value->name->latin,
                    'unit_kh' => $value->administrative_unit->km,
                    'unit_en' => $value->administrative_unit->en,
                    'unit_latin' => $value->administrative_unit->latin,
                    'parent_id' => intval(substr($key, 0, 6)),
                ]);
            }

            DB::commit();
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('geographies');
    }
};
