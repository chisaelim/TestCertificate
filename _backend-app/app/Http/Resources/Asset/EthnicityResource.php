<?php

namespace App\Http\Resources\Asset;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EthnicityResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'eth_en' => $this->eth_en,
            'eth_kh' => $this->eth_kh,
            'eth_label' => $this->eth_label,
        ];
    }
}
