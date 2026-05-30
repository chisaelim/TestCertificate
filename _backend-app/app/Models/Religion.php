<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Religion extends Model
{
    protected $table = 'religions';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $casts = [
        'id' => 'int',
    ];

    protected $fillable = [
        'rel_en',
        'rel_kh',
        'rel_label',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'religion_id');
    }
}
