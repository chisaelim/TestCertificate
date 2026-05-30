<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Geography extends Model
{
    protected $table = 'geographies';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
        'parent_id' => 'int',
    ];

    protected $fillable = [
        'name_en',
        'name_kh',
        'name_latin',
        'unit_en',
        'unit_kh',
        'unit_latin',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(Geography::class, 'parent_id');
    }

    public function childrens()
    {
        return $this->hasMany(Geography::class, 'parent_id');
    }
}
