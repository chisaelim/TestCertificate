<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Commune extends Model
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
        static::saving(function ($model) {
            $district = District::find($model->parent_id);
            $province = Province::find($district->parent_id);
            if ($province->unit_en === 'Capital') {
                $model->unit_kh = 'សង្កាត់';
                $model->unit_en = 'Quarter';
                $model->unit_latin = 'Sangkat';
            } else {
                $model->unit_kh = 'ឃុំ';
                $model->unit_en = 'Commune';
                $model->unit_latin = 'Khum';
            }
        });
        static::addGlobalScope('boot', function (Builder $builder) {
            $builder->whereIn('unit_en', ['Commune', 'Quarter'])
                ->orderBy('name_kh', 'asc');
        });
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'parent_id');
    }

    public function villages()
    {
        return $this->hasMany(Village::class, 'parent_id');
    }
}
