<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Village extends Model
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
            $model->unit_kh = 'ភូមិ';
            $model->unit_en = 'Village';
            $model->unit_latin = 'Phum';
        });
        static::addGlobalScope('boot', function (Builder $builder) {
            $builder->where('unit_en', 'Village')
                ->orderBy('name_kh', 'asc');
        });
    }

    public function commune()
    {
        return $this->belongsTo(Commune::class, 'parent_id');
    }
}
