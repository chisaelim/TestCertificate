<?php

namespace App\Http\Controllers\API;

use Exception;
use Throwable;
use App\Models\Commune;
use App\Models\Village;
use App\Models\District;
use App\Models\Province;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Geography\CreateCommuneRequest;
use App\Http\Requests\Geography\UpdateCommuneRequest;
use App\Http\Requests\Geography\CreateDistrictRequest;
use App\Http\Requests\Geography\UpdateDistrictRequest;
use App\Http\Requests\Geography\CreateProvinceRequest;
use App\Http\Requests\Geography\UpdateProvinceRequest;
use App\Http\Requests\Geography\CreateVillageRequest;
use App\Http\Requests\Geography\UpdateVillageRequest;
use App\Http\Requests\Geography\GetProvincesRequest;
use App\Http\Requests\Geography\GetDistrictsByProvinceIDRequest;
use App\Http\Requests\Geography\GetCommunesByDistrictIDRequest;
use App\Http\Requests\Geography\GetVillagesByCommuneIDRequest;
use App\Http\Requests\Geography\ReadVillageRequest;
use App\Http\Requests\Geography\DeleteVillageRequest;
use App\Http\Requests\Geography\ReadCommuneRequest;
use App\Http\Requests\Geography\DeleteCommuneRequest;
use App\Http\Requests\Geography\ReadDistrictRequest;
use App\Http\Requests\Geography\DeleteDistrictRequest;
use App\Http\Requests\Geography\ReadProvinceRequest;
use App\Http\Requests\Geography\DeleteProvinceRequest;
use App\Http\Resources\Geography\CommuneResource;
use App\Http\Resources\Geography\DistrictResource;
use App\Http\Resources\Geography\ProvinceResource;
use App\Http\Resources\Geography\VillageResource;

class GeographyController extends Controller
{
    public function getProvinces(GetProvincesRequest $request)
    {
        $provinces = Cache::rememberForever('provincesCache', function () {
            return ProvinceResource::collection(Province::all());
        });
        return response(['provinces' => $provinces], 200);
    }
    public function getDistrictsByProvinceID(GetDistrictsByProvinceIDRequest $request, $id)
    {
        $districts = Cache::rememberForever('districtsCache' . $id, function () use ($id) {
            return DistrictResource::collection(District::where('id_parent', $id)->get());
        });
        return response(['districts' => $districts], 200);
    }
    public function getCommunesByDistrictID(GetCommunesByDistrictIDRequest $request, $id)
    {
        $communes = Cache::rememberForever('communesCache' . $id, function () use ($id) {
            return CommuneResource::collection(Commune::where('id_parent', $id)->get());
        });
        return response(['communes' => $communes], 200);
    }
    public function getVillagesByCommuneID(GetVillagesByCommuneIDRequest $request, $id)
    {
        $villages = Cache::rememberForever('villagesCache' . $id, function () use ($id) {
            return VillageResource::collection(Village::where('id_parent', $id)->get());
        });
        return response(['villages' => $villages], 200);
    }
}
