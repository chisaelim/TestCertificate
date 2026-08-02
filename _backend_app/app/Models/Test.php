<?php

namespace App\Models;

use App\Traits\TimeStamps;
use App\Traits\TracksCreator;
use App\Traits\TracksUpdater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
    use TimeStamps, TracksCreator, TracksUpdater;

    protected $table = 'tests';
    protected $primaryKey = 'id';

    protected $casts = [
        'id' => 'int',
        'created_by' => 'int',
        'updated_by' => 'int',
    ];

    protected $fillable = [
        'name_en',
        'name_kh',
        'short_name',
        'created_by',
        'updated_by',
    ];

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('boot', function (Builder $builder) {
            $builder->orderBy('tests.name_en');
        });
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
        return $this->hasMany(StudentTest::class, 'test_id');
    }
}
