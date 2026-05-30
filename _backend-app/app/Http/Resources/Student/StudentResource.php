<?php

namespace App\Http\Resources\Student;

use App\Http\Resources\Asset\EthnicityResource;
use App\Http\Resources\Asset\GenderResource;
use App\Http\Resources\Asset\NationalityResource;
use App\Http\Resources\Asset\ReligionResource;
use App\Http\Resources\User\UserResource;
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
            'job' => $this->job,
            'home_no' => $this->home_no,
            'street_no' => $this->street_no,
            'phone' => $this->phone,
            'image' => $this->image,
            'thumbnail' => $this->thumbnail,
            'pob' => $this->pob,
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
