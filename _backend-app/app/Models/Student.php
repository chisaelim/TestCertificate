<?php

namespace App\Models;

use App\Services\ImageClassService;
use Carbon\Carbon;
use App\Traits\TimeStamps;
use App\Traits\TracksCreator;
use App\Traits\TracksUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Storage;

class Student extends Model
{
    use TimeStamps, TracksCreator, TracksUpdater;

    protected $table = 'students';
    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'int',
        'gender_id' => 'int',
        'nationality_id' => 'int',
        'ethnicity_id' => 'int',
        'religion_id' => 'int',
        'place_of_birth_id' => 'int',
        'place_of_residence_id' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];

    protected $fillable = [
        'name_en',
        'name_kh',
        'dob',
        'home_no',
        'street_no',
        'phone',
        'photo',
        'gender_id',
        'nationality_id',
        'ethnicity_id',
        'religion_id',
        'place_of_birth_id',
        'place_of_residence_id',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('boot', function (Builder $builder) {
            $builder->orderBy('students.name_en');
        });
    }

    protected function nameEn(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => strtoupper($value),
            set: fn(string $value) => strtoupper($value),
        );
    }

    protected function dob(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => Carbon::parse($value)->format('d-m-Y'),
            set: fn(string $value) => Carbon::parse($value)->format('Y-m-d'),
        );
    }

    // photo related methods and attributes
    protected function photo(): Attribute
    {
        return Attribute::make(
            get: function () {
                $imageClass = ImageClassService::forStudentModel();
                $imagePath = $this->getRawOriginal('photo');
                return $imageClass->fullUrl($imagePath);
            },
        );
    }

    protected function thumbnail(): Attribute
    {
        return Attribute::make(
            get: function () {
                $imageClass = ImageClassService::forStudentModel();
                $thumbnailPath = $imageClass->thumbnailPath($this->getRawOriginal('photo'));
                return $imageClass->fullUrl($thumbnailPath);
            },
        );
    }
    // end photo related methods and attributes

    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id');
    }

    public function nationality()
    {
        return $this->belongsTo(Nationality::class, 'nationality_id');
    }

    public function ethnicity()
    {
        return $this->belongsTo(Ethnicity::class, 'ethnicity_id');
    }

    public function religion()
    {
        return $this->belongsTo(Religion::class, 'religion_id');
    }

    public function placeOfBirth()
    {
        return $this->belongsTo(Geography::class, 'place_of_birth_id');
    }

    public function placeOfResidence()
    {
        return $this->belongsTo(Geography::class, 'place_of_residence_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function studentTests()
    {
        return $this->hasMany(StudentTest::class, 'student_id');
    }

    protected function getPobVillageIDAttribute()
    {
        return $this->pobVillage()?->id;
    }
    protected function getPobCommuneIDAttribute()
    {
        return $this->pobCommune()?->id;
    }
    protected function getPobDistrictIDAttribute()
    {
        return $this->pobDistrict()?->id;
    }
    protected function getPobProvinceIDAttribute()
    {
        return $this->pobProvince()?->id;
    }
    protected function pobVillage()
    {
        return Village::find($this->place_of_birth_id);
    }
    protected function pobCommune()
    {
        return Commune::find($this->pobVillage()?->parent_id ?? $this->place_of_birth_id);
    }
    protected function pobDistrict()
    {
        return District::find($this->pobCommune()?->parent_id ?? $this->place_of_birth_id);
    }
    protected function pobProvince()
    {
        return Province::find($this->pobDistrict()?->parent_id ?? $this->place_of_birth_id);
    }




    protected function getPorVillageIDAttribute()
    {
        return $this->porVillage()?->id;
    }
    protected function getPorCommuneIDAttribute()
    {
        return $this->porCommune()?->id;
    }
    protected function getPorDistrictIDAttribute()
    {
        return $this->porDistrict()?->id;
    }
    protected function getPorProvinceIDAttribute()
    {
        return $this->porProvince()?->id;
    }
    protected function porVillage()
    {
        return Village::find($this->place_of_residence_id);
    }
    protected function porCommune()
    {
        return Commune::find($this->porVillage()?->parent_id ?? $this->place_of_residence_id);
    }
    protected function porDistrict()
    {
        return District::find($this->porCommune()?->parent_id ?? $this->place_of_residence_id);
    }
    protected function porProvince()
    {
        return Province::find($this->porDistrict()?->parent_id ?? $this->place_of_residence_id);
    }
}
