<?php

namespace App\Models;

use Carbon\Carbon;
use App\Traits\TimeStamps;
use App\Traits\TracksCreator;
use App\Traits\TracksUpdater;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class StudentTest extends Model
{
    use TimeStamps, TracksCreator, TracksUpdater;

    protected $table = 'student_tests';
    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'int',
        'student_id' => 'int',
        'test_id' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];

    protected $fillable = [
        'issued_date',
        'expired_date',
        'status',
        'student_id',
        'test_id',
        'created_by',
        'updated_by',
    ];

    protected function issuedDate(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->format('d-m-Y') : null,
            set: fn(?string $value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    protected function expiredDate(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => $value ? Carbon::parse($value)->format('d-m-Y') : null,
            set: fn(?string $value) => $value ? Carbon::parse($value)->format('Y-m-d') : null,
        );
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function test()
    {
        return $this->belongsTo(Test::class, 'test_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
