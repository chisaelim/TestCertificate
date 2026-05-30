<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Nationality extends Model
{
    protected $table = 'nationalities';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'nat_en',
        'nat_kh',
        'nat_label',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'nationality_id');
    }
}
