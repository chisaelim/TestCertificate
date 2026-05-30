<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\Student\DetailStudentResource;
use App\Services\ImageClassService;
use Exception;
use Illuminate\Http\Request;
use Throwable;
use App\Models\Student;
use App\Models\Province;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\CreateStudentRequest;
use App\Http\Requests\Student\UpdateStudentRequest;
use Illuminate\Validation\ValidationException;

class StudentController extends Controller
{
    private $referenceEagerLoading = [
        'gender',
    ];

    private $manageEagerLoading = [
        'gender',
        'creator',
        'updater',
    ];

    private $readEagerLoading = [
        'gender',
        'nationality',
        'ethnicity',
        'religion',
        'creator',
        'updater',
    ];

    public function getStudents(Request $request)
    {
        $keyword = $request->input('keyword', null);
        $students = Student::where(function ($query) use ($keyword) {
            if ($keyword) {
                $query->where('name_kh', 'like', '%' . $keyword . '%')
                    ->orWhere('name_en', 'like', '%' . $keyword . '%')
                    ->orWhere('phone', 'like', '%' . $keyword . '%');
            }
        })
            ->with($this->referenceEagerLoading)
            ->limit(50)
            ->get();
        return response(
            [
                'students' => StudentResource::collection($students),
            ],
            200,
        );
    }

    public function getStudentsWithDetails()
    {
        $students = Student::with($this->manageEagerLoading)->get();

        return response(
            [
                'students' => DetailStudentResource::collection($students),
            ],
            200,
        );
    }

    public function createStudent(CreateStudentRequest $request)
    {
        $imageClass = ImageClassService::forStudentModel();
        $validated = $request->validated();

        $place_of_birth_id = $this->resolveGeography(
            $validated['pob_province_id'] ?? null,
            $validated['pob_district_id'] ?? null,
            $validated['pob_commune_id'] ?? null,
            $validated['pob_village_id'] ?? null,
        );

        $place_of_residence_id = $this->resolveGeography(
            $validated['por_province_id'] ?? null,
            $validated['por_district_id'] ?? null,
            $validated['por_commune_id'] ?? null,
            $validated['por_village_id'] ?? null,
        );

        $phone = $validated['phone'];
        $existed = Student::where('phone', $phone)->first();
        if ($existed && strcasecmp($existed->phone, $phone) === 0) {
            throw ValidationException::withMessages([
                'phone' => 'លេខទូរស័ព្ទមានក្នុងប្រព័ន្ធរួចហើយ។',
            ]);
        }

        try {
            DB::beginTransaction();

            $newImage = null;
            if (!empty($validated['image'])) {
                $newImage = $imageClass->store($request->file('image'));
            }

            $student = Student::create([
                'name_kh' => $validated['name_kh'],
                'name_en' => strtoupper($validated['name_en']),
                'dob' => $validated['dob'],
                'home_no' => $validated['home_no'],
                'street_no' => $validated['street_no'],
                'phone' => $phone,
                'photo' => $newImage,
                'gender_id' => $validated['gender_id'],
                'ethnicity_id' => $validated['ethnicity_id'],
                'nationality_id' => $validated['nationality_id'],
                'religion_id' => $validated['religion_id'],
                'place_of_birth_id' => $place_of_birth_id,
                'place_of_residence_id' => $place_of_residence_id,
            ]);

            $student = Student::where('id', $student->id)
                ->with($this->manageEagerLoading)
                ->first();

            DB::commit();
        } catch (Throwable $th) {
            $imageClass->delete($newImage);
            DB::rollback();
            throw $th;
        }

        return response(
            [
                'message' => 'The student has been created.',
                'student' => new DetailStudentResource($student),
            ],
            201,
        );
    }

    public function readStudent($id)
    {
        $student = Student::where('id', $id)
            ->with($this->readEagerLoading)
            ->first();

        if (!$student) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'student' => new DetailStudentResource($student),
            ],
            200,
        );
    }

    public function updateStudent(UpdateStudentRequest $request)
    {

        $imageClass = ImageClassService::forStudentModel();
        $newImage = null;
        $validated = $request->validated();
        $id = $validated['id'];

        $student = Student::find($id);
        if (!$student) {
            return ResponseHelper::notFoundErrorMsg();
        }
        $oldImage = $student->getRawOriginal('photo');


        $place_of_birth_id = $this->resolveGeography(
            $validated['pob_province_id'] ?? null,
            $validated['pob_district_id'] ?? null,
            $validated['pob_commune_id'] ?? null,
            $validated['pob_village_id'] ?? null,
        );

        $place_of_residence_id = $this->resolveGeography(
            $validated['por_province_id'] ?? null,
            $validated['por_district_id'] ?? null,
            $validated['por_commune_id'] ?? null,
            $validated['por_village_id'] ?? null,
        );

        $phone = $validated['phone'];
        $existed = Student::where('phone', $phone)->first();
        if ($existed && $existed->id !== $id) {
            if (strcasecmp($existed->phone, $phone) === 0) {
                throw ValidationException::withMessages([
                    'phone' => 'លេខទូរស័ព្ទមានក្នុងប្រព័ន្ធរួចហើយ។',
                ]);
            }
        }

        try {
            DB::beginTransaction();

            if ($request->has('image')) {
                if (!empty($validated['image'])) {
                    $newImage = $imageClass->store($request->file('image'));
                }
                $student->photo = $newImage;
            }

            $student->name_kh = $validated['name_kh'];
            $student->name_en = strtoupper($validated['name_en']);
            $student->dob = $validated['dob'];
            $student->home_no = $validated['home_no'];
            $student->street_no = $validated['street_no'];
            $student->phone = $phone;
            $student->gender_id = $validated['gender_id'];
            $student->ethnicity_id = $validated['ethnicity_id'];
            $student->nationality_id = $validated['nationality_id'];
            $student->religion_id = $validated['religion_id'];
            $student->place_of_birth_id = $place_of_birth_id;
            $student->place_of_residence_id = $place_of_residence_id;

            $updated = $student->save();
            if (!$updated) {
                $imageClass->delete($newImage);
                DB::rollback();
                return ResponseHelper::updateErrorMsg();
            }

            DB::commit();

            // Delete old image only after DB commit succeeds
            if ($request->has('image')) {
                $imageClass->delete($oldImage);
            }
        } catch (Throwable $th) {
            $imageClass->delete($newImage);
            DB::rollback();
            throw $th;
        }

        return response(
            [
                'message' => 'The student has been updated.',
                'student' => new DetailStudentResource($student->load($this->manageEagerLoading)),
            ],
            200,
        );
    }

    public function deleteStudent($id)
    {
        $student = Student::where('id', $id)
            ->with($this->manageEagerLoading)
            ->first();

        if (!$student) {
            return ResponseHelper::notFoundErrorMsg();
        }

        try {
            $student->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        } catch (Throwable $th) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response(
            [
                'message' => 'The student has been deleted.',
                'student' => new DetailStudentResource($student),
            ],
            200,
        );
    }

    private function resolveGeography($province_id, $district_id, $commune_id, $village_id)
    {
        $geography_id = null;

        if ($province_id) {
            $province = Province::find($province_id);
            if (!$province) {
                return ResponseHelper::requirementErrorMsg();
            }
            $geography_id = $province->id;
        }

        if ($district_id) {
            $district = $province->districts->find($district_id);
            if (!$district) {
                return ResponseHelper::requirementErrorMsg();
            }
            $geography_id = $district->id;
        }

        if ($commune_id) {
            $commune = $district->communes->find($commune_id);
            if (!$commune) {
                return ResponseHelper::requirementErrorMsg();
            }
            $geography_id = $commune->id;
        }

        if ($village_id) {
            $village = $commune->villages->find($village_id);
            if (!$village) {
                return ResponseHelper::requirementErrorMsg();
            }
            $geography_id = $village->id;
        }

        return $geography_id;
    }
}
