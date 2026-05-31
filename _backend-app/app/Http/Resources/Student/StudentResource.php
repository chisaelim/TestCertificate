<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\Asset\EthnicityResource;
use App\Http\Resources\Asset\GenderResource;
use App\Http\Resources\Asset\NationalityResource;
use App\Http\Resources\Asset\ReligionResource;
use App\Http\Resources\Geography\CommuneResource;
use App\Http\Resources\Geography\DistrictResource;
use App\Http\Resources\Geography\ProvinceResource;
use App\Http\Resources\Geography\VillageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name_en' => $this->name_en,
            'name_kh' => $this->name_kh,
            'dob' => $this->dob,
            'home_no' => $this->home_no,
            'street_no' => $this->street_no,
            'phone' => $this->phone,
            'photo' => $this->photo,
            'thumbnail' => $this->thumbnail,
            'pob_village' => $this->whenLoaded('placeOfBirth', fn() => new VillageResource(
                $this->placeOfBirth?->parent?->parent?->parent ? $this->placeOfBirth : null
            )),
            'pob_commune' => $this->whenLoaded('placeOfBirth', fn() => new CommuneResource(
                $this->placeOfBirth?->parent?->parent?->parent ? $this->placeOfBirth->parent
                : ($this->placeOfBirth?->parent?->parent ? $this->placeOfBirth : null)
            )),
            'pob_district' => $this->whenLoaded('placeOfBirth', fn() => new DistrictResource(
                $this->placeOfBirth?->parent?->parent?->parent ? $this->placeOfBirth->parent->parent
                : ($this->placeOfBirth?->parent?->parent ? $this->placeOfBirth->parent
                    : ($this->placeOfBirth?->parent ? $this->placeOfBirth : null))
            )),
            'pob_province' => $this->whenLoaded('placeOfBirth', fn() => new ProvinceResource(
                $this->placeOfBirth?->parent?->parent?->parent ? $this->placeOfBirth->parent->parent->parent
                : ($this->placeOfBirth?->parent?->parent ? $this->placeOfBirth->parent->parent
                    : ($this->placeOfBirth?->parent ? $this->placeOfBirth->parent : $this->placeOfBirth))
            )),
            'por_village' => $this->whenLoaded('placeOfResidence', fn() => new VillageResource(
                $this->placeOfResidence?->parent?->parent?->parent ? $this->placeOfResidence : null
            )),
            'por_commune' => $this->whenLoaded('placeOfResidence', fn() => new CommuneResource(
                $this->placeOfResidence?->parent?->parent?->parent ? $this->placeOfResidence->parent
                : ($this->placeOfResidence?->parent?->parent ? $this->placeOfResidence : null)
            )),
            'por_district' => $this->whenLoaded('placeOfResidence', fn() => new DistrictResource(
                $this->placeOfResidence?->parent?->parent?->parent ? $this->placeOfResidence->parent->parent
                : ($this->placeOfResidence?->parent?->parent ? $this->placeOfResidence->parent
                    : ($this->placeOfResidence?->parent ? $this->placeOfResidence : null))
            )),
            'por_province' => $this->whenLoaded('placeOfResidence', fn() => new ProvinceResource(
                $this->placeOfResidence?->parent?->parent?->parent ? $this->placeOfResidence->parent->parent->parent
                : ($this->placeOfResidence?->parent?->parent ? $this->placeOfResidence->parent->parent
                    : ($this->placeOfResidence?->parent ? $this->placeOfResidence->parent : $this->placeOfResidence))
            )),
            'gender' => $this->whenLoaded('gender', fn() => new GenderResource($this->gender)),
            'nationality' => $this->whenLoaded('nationality', fn() => new NationalityResource($this->nationality)),
            'ethnicity' => $this->whenLoaded('ethnicity', fn() => new EthnicityResource($this->ethnicity)),
            'religion' => $this->whenLoaded('religion', fn() => new ReligionResource($this->religion)),
        ];
    }
}
