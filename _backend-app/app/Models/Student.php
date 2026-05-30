<?php

namespace App\Models;

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
        'job',
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

    protected function photo5x5(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->photo ? Storage::disk('public')->url("images/students/5x5/{$this->photo}") : null
        );
    }

    protected function photo3x4(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->photo ? Storage::disk('public')->url("images/students/3x4/{$this->photo}") : null
        );
    }

    protected function photo4x6(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->photo ? Storage::disk('public')->url("images/students/4x6/{$this->photo}") : null
        );
    }

    protected function thumbNail(): Attribute
    {
        return Attribute::make(
            get: fn() => $this->photo ? Storage::disk('public')->url("images/students/thumbnails/{$this->photo}") : null
        );
    }

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
}
