<?php

namespace App\Http\Resources\Asset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'gd_en' => $this->gd_en,
            'gd_kh' => $this->gd_kh,
            'gd_en_full' => $this->gd_en_full,
            'gd_kh_full' => $this->gd_kh_full,
        ];
    }
}
