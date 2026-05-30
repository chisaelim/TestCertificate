<?php

namespace App\Http\Resources\Asset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class NationalityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nat_en' => $this->nat_en,
            'nat_kh' => $this->nat_kh,
            'nat_label' => $this->nat_label,
        ];
    }
}
