<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class District extends Model
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
            $province = Province::find($model->parent_id);
            if ($province->unit_en === 'Capital') {
                $model->unit_kh = 'ខណ្ឌ';
                $model->unit_en = 'Section';
                $model->unit_latin = 'Khan';
            } else {
                if ($model->unit_en === 'District') {
                    $model->unit_kh = 'ស្រុក';
                    // $model->unit_en = 'District';
                    $model->unit_latin = 'Srok';
                } else {
                    $model->unit_kh = 'ក្រុង';
                    // $model->unit_en = 'Municipality';
                    $model->unit_latin = 'Krong';
                }
            }
        });
        static::addGlobalScope('boot', function (Builder $builder) {
            $builder->whereIn('unit_en', ['District', 'Section', 'Municipality'])
                ->orderBy('name_kh', 'asc');
        });
    }

    public function province()
    {
        return $this->belongsTo(Province::class, 'parent_id');
    }

    public function communes()
    {
        return $this->hasMany(Commune::class, 'parent_id');
    }
}
