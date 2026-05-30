<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\Asset\EthnicityResource;
use App\Http\Resources\Asset\GenderResource;
use App\Http\Resources\Asset\NationalityResource;
use App\Http\Resources\Asset\ReligionResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailStudentResource extends JsonResource
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
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,

            'gender_id' => $this->gender_id,
            'nationality_id' => $this->nationality_id,
            'ethnicity_id' => $this->ethnicity_id,
            'religion_id' => $this->religion_id,
            'pob_village_id' => $this->pob_village_id,
            'pob_commune_id' => $this->pob_commune_id,
            'pob_district_id' => $this->pob_district_id,
            'pob_province_id' => $this->pob_province_id,
            'por_village_id' => $this->por_village_id,
            'por_commune_id' => $this->por_commune_id,
            'por_district_id' => $this->por_district_id,
            'por_province_id' => $this->por_province_id,

            'gender' => $this->whenLoaded('gender', fn() => new GenderResource($this->gender)),
            'nationality' => $this->whenLoaded('nationality', fn() => new NationalityResource($this->nationality)),
            'ethnicity' => $this->whenLoaded('ethnicity', fn() => new EthnicityResource($this->ethnicity)),
            'religion' => $this->whenLoaded('religion', fn() => new ReligionResource($this->religion)),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'creator' => $this->whenLoaded('creator', fn() => new UserResource($this->creator)),
            'updater' => $this->whenLoaded('updater', fn() => new UserResource($this->updater)),
        ];
    }
}
