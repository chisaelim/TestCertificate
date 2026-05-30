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
            return Province::all();
        });
        return response(['provinces' => ProvinceResource::collection($provinces)], 200);
    }
    public function getDistrictsByProvinceID(GetDistrictsByProvinceIDRequest $request, $id_geography)
    {
        $districts = Cache::rememberForever('districtsCache' . $id_geography, function () use ($id_geography) {
            return District::where('id_parent', $id_geography)->get();
        });
        return response(['districts' => DistrictResource::collection($districts)], 200);
    }
    public function getCommunesByDistrictID(GetCommunesByDistrictIDRequest $request, $id_geography)
    {
        $communes = Cache::rememberForever('communesCache' . $id_geography, function () use ($id_geography) {
            return Commune::where('id_parent', $id_geography)->get();
        });
        return response(['communes' => CommuneResource::collection($communes)], 200);
    }
    public function getVillagesByCommuneID(GetVillagesByCommuneIDRequest $request, $id_geography)
    {
        $villages = Cache::rememberForever('villagesCache' . $id_geography, function () use ($id_geography) {
            return Village::where('id_parent', $id_geography)->get();
        });
        return response(['villages' => VillageResource::collection($villages)], 200);
    }

    public function createVillage(CreateVillageRequest $request)
    {
        $validated = $request->validated();

        $this->validateVillageInputs($request);

        $village = Village::create([
            'name_kh' => $validated['name_kh'],
            'name_en' => $validated['name_en'],
            'name_latin' => $validated['name_latin'],
            'id_parent' => $validated['id_commune'],
        ]);

        $village = Village::where('id_geography', $village->id_geography)->first();

        return response(
            [
                'message' => 'The village has been created.',
                'village' => new VillageResource($village),
            ],
            201,
        );
    }
    public function updateVillage(UpdateVillageRequest $request)
    {
        $validated = $request->validated();

        $village = $this->validateVillageInputs($request);

        $village->name_kh = $validated['name_kh'];
        $village->name_en = $validated['name_en'];
        $village->name_latin = $validated['name_latin'];
        $updated = $village->save();
        if (!$updated) {
            return ResponseHelper::updateErrorMsg();
        }
        $village = Village::where('id_geography', $village->id_geography)->first();

        return response(
            [
                'message' => 'The village has been updated.',
                'village' => new VillageResource($village),
            ],
            200,
        );
    }
    public function readVillage(ReadVillageRequest $request, $id_village)
    {
        $village = Village::find($id_village);
        if (!$village) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'message' => 'Village details retrieved.',
                'village' => new VillageResource($village),
            ],
            200,
        );
    }
    public function deleteVillage(DeleteVillageRequest $request, $id_village)
    {
        $village = Village::where('id_geography', $id_village)->first();
        if (!$village) {
            return ResponseHelper::notFoundErrorMsg();
        }
        try {
            $village->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        } catch (Throwable $th) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response(
            [
                'message' => 'The village has been deleted.',
                'village' => new VillageResource($village),
            ],
            200,
        );
    }
    private function validateVillageInputs($request)
    {
        $existeds = Village::where(function ($q) use ($request) {
            $q->where('name_kh', $request->name_kh)
                ->orWhere('name_en', $request->name_en)
                ->orWhere('name_latin', $request->name_latin);
        })
            ->whereHas('commune', function ($q) use ($request) {
                $q->where('id_geography', $request->id_commune)
                    ->whereHas('district', function ($q) use ($request) {
                        $q->where('id_geography', $request->id_district)
                            ->whereHas('province', function ($q) use ($request) {
                                $q->where('id_geography', $request->id_province);
                            });
                    });
            })
            ->get();
        $errors = [];
        foreach ($existeds as $existed) {
            if ($existed->id_geography !== $request->id_village) {
                if ($request->name_kh && strcasecmp($existed->name_kh, $request->name_kh) === 0) {
                    $errors['name_kh'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_en && strcasecmp($existed->name_en, $request->name_en) === 0) {
                    $errors['name_en'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_latin && strcasecmp($existed->name_latin, $request->name_latin) === 0) {
                    $errors['name_latin'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
            }
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
        if ($request->id_village) {
            $village = Village::where('id_geography', $request->id_village)
                ->whereHas('commune', function ($q) use ($request) {
                    $q->where('id_geography', $request->id_commune)
                        ->whereHas('district', function ($q) use ($request) {
                            $q->where('id_geography', $request->id_district)
                                ->whereHas('province', function ($q) use ($request) {
                                    $q->where('id_geography', $request->id_province);
                                });
                        });
                })->first();
            if (!$village) {
                return ResponseHelper::notFoundErrorMsg();
            }
            return $village;
        }

    }

    public function createCommune(CreateCommuneRequest $request)
    {
        $validated = $request->validated();

        $this->validateCommuneInputs($request);

        $commune = Commune::create([
            'name_kh' => $validated['name_kh'],
            'name_en' => $validated['name_en'],
            'name_latin' => $validated['name_latin'],
            'id_parent' => $validated['id_district'],
        ]);

        $commune = Commune::where('id_geography', $commune->id_geography)->first();

        return response(
            [
                'message' => 'The commune has been created.',
                'commune' => new CommuneResource($commune),
            ],
            201,
        );
    }
    public function updateCommune(UpdateCommuneRequest $request)
    {
        $validated = $request->validated();

        $commune = $this->validateCommuneInputs($request);

        $commune->name_kh = $validated['name_kh'];
        $commune->name_en = $validated['name_en'];
        $commune->name_latin = $validated['name_latin'];
        $updated = $commune->save();
        if (!$updated) {
            return ResponseHelper::updateErrorMsg();
        }
        $commune = Commune::where('id_geography', $commune->id_geography)->first();

        return response(
            [
                'message' => 'The commune has been updated.',
                'commune' => new CommuneResource($commune),
            ],
            200,
        );
    }
    public function readCommune(ReadCommuneRequest $request, $id_commune)
    {
        $commune = Commune::find($id_commune);
        if (!$commune) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'message' => 'Commune details retrieved.',
                'commune' => new CommuneResource($commune),
            ],
            200,
        );
    }
    public function deleteCommune(DeleteCommuneRequest $request, $id_commune)
    {
        $commune = Commune::where('id_geography', $id_commune)->first();
        if (!$commune) {
            return ResponseHelper::notFoundErrorMsg();
        }
        try {
            $commune->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        } catch (Throwable $th) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response(
            [
                'message' => 'The commune has been deleted.',
                'commune' => new CommuneResource($commune),
            ],
            200,
        );
    }
    private function validateCommuneInputs($request)
    {
        $existeds = Commune::where(function ($q) use ($request) {
            $q->where('name_kh', $request->name_kh)
                ->orWhere('name_en', $request->name_en)
                ->orWhere('name_latin', $request->name_latin);
        })->whereHas('district', function ($q) use ($request) {
            $q->where('id_geography', $request->id_district)
                ->whereHas('province', function ($q) use ($request) {
                    $q->where('id_geography', $request->id_province);
                });
        })->get();
        $errors = [];
        foreach ($existeds as $existed) {
            if ($existed->id_geography !== $request->id_commune) {
                if ($request->name_kh && strcasecmp($existed->name_kh, $request->name_kh) === 0) {
                    $errors['name_kh'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_en && strcasecmp($existed->name_en, $request->name_en) === 0) {
                    $errors['name_en'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_latin && strcasecmp($existed->name_latin, $request->name_latin) === 0) {
                    $errors['name_latin'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
            }
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
        if ($request->id_commune) {
            $commune = Commune::where('id_geography', $request->id_commune)
                ->whereHas('district', function ($q) use ($request) {
                    $q->where('id_geography', $request->id_district)
                        ->whereHas('province', function ($q) use ($request) {
                            $q->where('id_geography', $request->id_province);
                        });
                })->first();
            if (!$commune) {
                return ResponseHelper::notFoundErrorMsg();
            }
            return $commune;
        }

    }

    public function createDistrict(CreateDistrictRequest $request)
    {
        $validated = $request->validated();

        $this->validateDistrictInputs($request);

        $district = District::create([
            'name_kh' => $validated['name_kh'],
            'name_en' => $validated['name_en'],
            'name_latin' => $validated['name_latin'],
            'unit_en' => $validated['unit_en'],
            'id_parent' => $validated['id_province'],
        ]);

        $district = District::where('id_geography', $district->id_geography)->first();

        return response(
            [
                'message' => 'The district has been created.',
                'district' => new DistrictResource($district),
            ],
            201,
        );
    }
    public function updateDistrict(UpdateDistrictRequest $request)
    {
        $validated = $request->validated();

        $district = $this->validateDistrictInputs($request);

        $district->name_kh = $validated['name_kh'];
        $district->name_en = $validated['name_en'];
        $district->name_latin = $validated['name_latin'];
        $district->unit_en = $validated['unit_en'];
        $updated = $district->save();
        if (!$updated) {
            return ResponseHelper::updateErrorMsg();
        }
        $district = District::where('id_geography', $district->id_geography)->first();

        return response(
            [
                'message' => 'The district has been updated.',
                'district' => new DistrictResource($district),
            ],
            200,
        );
    }
    public function readDistrict(ReadDistrictRequest $request, $id_district)
    {
        $district = District::find($id_district);
        if (!$district) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'message' => 'District details retrieved.',
                'district' => new DistrictResource($district),
            ],
            200,
        );
    }
    public function deleteDistrict(DeleteDistrictRequest $request, $id_district)
    {
        $district = District::where('id_geography', $id_district)->first();
        if (!$district) {
            return ResponseHelper::notFoundErrorMsg();
        }
        try {
            $district->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        } catch (Throwable $th) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response(
            [
                'message' => 'The district has been deleted.',
                'district' => new DistrictResource($district),
            ],
            200,
        );
    }
    private function validateDistrictInputs($request)
    {
        $existeds = District::where(function ($q) use ($request) {
            $q->where('name_kh', $request->name_kh)
                ->orWhere('name_en', $request->name_en)
                ->orWhere('name_latin', $request->name_latin);
        })
            ->whereHas('province', function ($q) use ($request) {
                $q->where('id_geography', $request->id_province);

            })
            ->get();
        $errors = [];
        foreach ($existeds as $existed) {
            if ($existed->id_geography !== $request->id_district) {
                if ($request->name_kh && strcasecmp($existed->name_kh, $request->name_kh) === 0) {
                    $errors['name_kh'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_en && strcasecmp($existed->name_en, $request->name_en) === 0) {
                    $errors['name_en'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_latin && strcasecmp($existed->name_latin, $request->name_latin) === 0) {
                    $errors['name_latin'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
            }
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
        if ($request->id_district) {
            $district = District::where('id_geography', $request->id_district)
                ->whereHas('province', function ($q) use ($request) {
                    $q->where('id_geography', $request->id_province);
                })->first();
            if (!$district) {
                return ResponseHelper::notFoundErrorMsg();
            }
            return $district;
        }

    }

    public function createProvince(CreateProvinceRequest $request)
    {
        $validated = $request->validated();

        $this->validateProvinceInputs($request);

        $province = Province::create([
            'name_kh' => $validated['name_kh'],
            'name_en' => $validated['name_en'],
            'name_latin' => $validated['name_latin'],
            'id_parent' => null,
        ]);

        $province = Province::where('id_geography', $province->id_geography)->first();

        return response(
            [
                'message' => 'The province has been created.',
                'province' => new ProvinceResource($province),
            ],
            201,
        );
    }
    public function updateProvince(UpdateProvinceRequest $request)
    {
        $validated = $request->validated();

        $province = $this->validateProvinceInputs($request);

        $province->name_kh = $validated['name_kh'];
        $province->name_en = $validated['name_en'];
        $province->name_latin = $validated['name_latin'];
        $updated = $province->save();
        if (!$updated) {
            return ResponseHelper::updateErrorMsg();
        }
        $province = Province::where('id_geography', $province->id_geography)->first();

        return response(
            [
                'message' => 'The province has been updated.',
                'province' => new ProvinceResource($province),
            ],
            200,
        );
    }
    public function readProvince(ReadProvinceRequest $request, $id_province)
    {
        $province = Province::find($id_province);
        if (!$province) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'message' => 'Province details retrieved.',
                'province' => new ProvinceResource($province),
            ],
            200,
        );
    }
    public function deleteProvince(DeleteProvinceRequest $request, $id_province)
    {
        $province = Province::where('id_geography', $id_province)->first();
        if (!$province) {
            return ResponseHelper::notFoundErrorMsg();
        }
        try {
            $province->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        } catch (Throwable $th) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response(
            [
                'message' => 'The province has been deleted.',
                'province' => new ProvinceResource($province),
            ],
            200,
        );
    }
    private function validateProvinceInputs($request)
    {
        $existeds = Province::where(function ($q) use ($request) {
            $q->where('name_kh', $request->name_kh)
                ->orWhere('name_en', $request->name_en)
                ->orWhere('name_latin', $request->name_latin);
        })->get();
        $errors = [];
        foreach ($existeds as $existed) {
            if ($existed->id_geography !== $request->id_province) {
                if ($request->name_kh && strcasecmp($existed->name_kh, $request->name_kh) === 0) {
                    $errors['name_kh'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_en && strcasecmp($existed->name_en, $request->name_en) === 0) {
                    $errors['name_en'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
                if ($request->name_latin && strcasecmp($existed->name_latin, $request->name_latin) === 0) {
                    $errors['name_latin'] = 'ទិន្នន័យនេះមានរួចហើយ។';
                }
            }
        }
        if ($errors) {
            throw ValidationException::withMessages($errors);
        }
        if ($request->id_province) {
            $province = Province::where('id_geography', $request->id_province)
                ->first();
            if (!$province) {
                return ResponseHelper::notFoundErrorMsg();
            }
            return $province;
        }

    }
}
