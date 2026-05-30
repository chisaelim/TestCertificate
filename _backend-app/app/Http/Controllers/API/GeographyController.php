<?php

namespace App\Http\Controllers\API;

use App\Models\Commune;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use App\Http\Requests\Geography\GetProvincesRequest;
use App\Http\Requests\Geography\GetDistrictsByProvinceIDRequest;
use App\Http\Requests\Geography\GetCommunesByDistrictIDRequest;
use App\Http\Requests\Geography\GetVillagesByCommuneIDRequest;
use App\Http\Resources\Geography\CommuneResource;
use App\Http\Resources\Geography\DistrictResource;
use App\Http\Resources\Geography\ProvinceResource;
use App\Http\Resources\Geography\VillageResource;

class GeographyController extends Controller
{
    public function getProvinces(GetProvincesRequest $request)
    {
        $provinces = Cache::rememberForever('provincesCache', function () {
            return ProvinceResource::collection(Province::all())->toArray(request());
        });
        return response(['provinces' => $provinces], 200);
    }
    public function getDistrictsByProvince(GetDistrictsByProvinceIDRequest $request, $id)
    {
        $districts = Cache::rememberForever('districtsCache' . $id, function () use ($id) {
            return DistrictResource::collection(District::where('parent_id', $id)->get())->toArray(request());
        });
        return response(['districts' => $districts], 200);
    }
    public function getCommunesByDistrict(GetCommunesByDistrictIDRequest $request, $id)
    {
        $communes = Cache::rememberForever('communesCache' . $id, function () use ($id) {
            return CommuneResource::collection(Commune::where('parent_id', $id)->get())->toArray(request());
        });
        return response(['communes' => $communes], 200);
    }
    public function getVillagesByCommune(GetVillagesByCommuneIDRequest $request, $id)
    {
        $villages = Cache::rememberForever('villagesCache' . $id, function () use ($id) {
            return VillageResource::collection(Village::where('parent_id', $id)->get())->toArray(request());
        });
        return response(['villages' => $villages], 200);
    }
}
