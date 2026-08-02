<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Models\District;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Province extends Model
{
    protected $table = 'geographies';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'parent_id' => 'int'
    ];

    protected $fillable = [
        'id',
        'name_en',
        'name_kh',
        'name_latin',
        'unit_en',
        'unit_kh',
        'unit_latin',
        'parent_id'
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->unit_kh = 'ខេត្ត';
            $model->unit_en = 'Province';
            $model->unit_latin = 'Khaet';
        });
        static::addGlobalScope('boot', function (Builder $builder) {
            $builder->whereIn('unit_en', ['Province', 'Capital'])
                ->orderBy('name_kh', 'asc');
        });
    }

    public function districts()
    {
        return $this->hasMany(District::class, 'parent_id');
    }
}
