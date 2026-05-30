<?php

namespace App\Http\Resources\Geography;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommuneResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_kh' => $this->name_kh,
            'name_en' => $this->name_en,
            'name_latin' => $this->name_latin,
            'unit_kh' => $this->unit_kh,
            'unit_en' => $this->unit_en,
            'unit_latin' => $this->unit_latin,
        ];
    }
}
