<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ethnicity extends Model
{
    protected $table = 'ethnicities';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'eth_en',
        'eth_kh',
        'eth_label',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'ethnicity_id');
    }
}
