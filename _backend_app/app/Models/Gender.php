<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'genders';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'gd_en',
        'gd_en_full',
        'gd_kh',
        'gd_kh_full',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'gender_id');
    }
}
