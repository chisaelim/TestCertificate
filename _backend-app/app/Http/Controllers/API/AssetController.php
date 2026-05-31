<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\Asset\EthnicityResource;
use App\Http\Resources\Asset\GenderResource;
use App\Http\Resources\Asset\NationalityResource;
use App\Http\Resources\Asset\ReligionResource;
use App\Models\Ethnicity;
use App\Models\Gender;
use App\Models\Nationality;
use App\Models\Religion;
use App\Http\Requests\Asset\GetAllGendersRequest;
use App\Http\Requests\Asset\GetAllEthnicitiesRequest;
use App\Http\Requests\Asset\GetAllNationalitiesRequest;
use App\Http\Requests\Asset\GetAllReligionsRequest;
use Illuminate\Support\Facades\Cache;

class AssetController extends Controller
{
    public function getAllGenders(GetAllGendersRequest $request)
    {
        $genders = Cache::rememberForever('gendersCache', function () {
            return GenderResource::collection(Gender::all())->toArray(request());
        });

        return response(
            [
                'genders' => $genders,
            ],
            200,
        );
    }

    public function getAllEthnicities(GetAllEthnicitiesRequest $request)
    {
        $ethnicities = Cache::rememberForever('ethnicitiesCache', function () {
            return EthnicityResource::collection(Ethnicity::all())->toArray(request());
        });

        return response(
            [
                'ethnicities' => $ethnicities,
            ],
            200,
        );
    }

    public function getAllNationalities(GetAllNationalitiesRequest $request)
    {
        $nationalities = Cache::rememberForever('nationalitiesCache', function () {
            return NationalityResource::collection(Nationality::all())->toArray(request());
        });

        return response(
            [
                'nationalities' => $nationalities,
            ],
            200,
        );
    }

    public function getAllReligions(GetAllReligionsRequest $request)
    {
        $religions = Cache::rememberForever('religionsCache', function () {
            return ReligionResource::collection(Religion::all())->toArray(request());
        });

        return response(
            [
                'religions' => $religions,
            ],
            200,
        );
    }
}
