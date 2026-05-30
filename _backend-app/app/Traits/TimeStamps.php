<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

trait TimeStamps
{
    protected function CreatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone('Asia/Phnom_Penh')->format('d-m-Y h:i:s A') : null,
        );
    }
    protected function UpdatedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone('Asia/Phnom_Penh')->format('d-m-Y h:i:s A') : null,
        );
    }

    protected function DeletedAt(): Attribute
    {
        return Attribute::make(
            get: fn($value) => $value ? Carbon::parse($value)->timezone('Asia/Phnom_Penh')->format('d-m-Y h:i:s A') : null,
        );
    }
}
