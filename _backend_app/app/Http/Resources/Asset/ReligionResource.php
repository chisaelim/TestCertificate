<?php

namespace App\Http\Resources\Asset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ReligionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'rel_en' => $this->rel_en,
            'rel_kh' => $this->rel_kh,
            'rel_label' => $this->rel_label,
        ];
    }
}
