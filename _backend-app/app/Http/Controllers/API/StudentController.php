<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\Student\StudentResource;
use App\Services\ImageService;
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
use App\Http\Resources\Student\ManageStudentResource;
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
                'students' => StudentResource::collection($students),
            ],
            200,
        );
    }

    public function createStudent(CreateStudentRequest $request)
    {
        $validated = $request->validated();

        $id_pob = $this->resolveGeography(
            $validated['id_pob_province'] ?? null,
            $validated['id_pob_district'] ?? null,
            $validated['id_pob_commune'] ?? null,
            $validated['id_pob_village'] ?? null,
        );

        $id_por = $this->resolveGeography(
            $validated['id_por_province'] ?? null,
            $validated['id_por_district'] ?? null,
            $validated['id_por_commune'] ?? null,
            $validated['id_por_village'] ?? null,
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

            $photo_name = null;
            if (!empty($validated['photo'])) {
                $photo_name = ImageService::storeProfileImage($validated['photo'], 'students');
            }

            $student = Student::create([
                'name_kh' => $validated['name_kh'],
                'name_en' => strtoupper($validated['name_en']),
                'dob' => $validated['dob'],
                'job' => $validated['job'],
                'home_no' => $validated['home_no'],
                'street_no' => $validated['street_no'],
                'phone' => $phone,
                'photo' => $photo_name,
                'id_gender' => $validated['id_gender'],
                'id_ethnicity' => $validated['id_ethnicity'],
                'id_nationality' => $validated['id_nationality'],
                'id_religion' => $validated['id_religion'],
                'id_pob' => $id_pob,
                'id_por' => $id_por,
            ]);

            $student = Student::where('id_student', $student->id_student)
                ->with($this->manageEagerLoading)
                ->first();

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        } catch (Throwable $th) {
            DB::rollback();
            throw $th;
        }

        return response(
            [
                'message' => 'The student has been created.',
                'student' => new ManageStudentResource($student),
            ],
            201,
        );
    }

    public function readStudent($id_student)
    {
        $student = Student::where('id_student', $id_student)
            ->with($this->readEagerLoading)
            ->first();

        if (!$student) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'student' => new ManageStudentResource($student),
            ],
            200,
        );
    }

    public function updateStudent(UpdateStudentRequest $request)
    {
        $validated = $request->validated();
        $id_student = $validated['id_student'];

        $student = Student::find($id_student);
        if (!$student) {
            return ResponseHelper::notFoundErrorMsg();
        }

        $id_pob = $this->resolveGeography(
            $validated['id_pob_province'] ?? null,
            $validated['id_pob_district'] ?? null,
            $validated['id_pob_commune'] ?? null,
            $validated['id_pob_village'] ?? null,
        );

        $id_por = $this->resolveGeography(
            $validated['id_por_province'] ?? null,
            $validated['id_por_district'] ?? null,
            $validated['id_por_commune'] ?? null,
            $validated['id_por_village'] ?? null,
        );

        $phone = $validated['phone'];
        $existed = Student::where('phone', $phone)->first();
        if ($existed && $existed->id_student !== $id_student) {
            if (strcasecmp($existed->phone, $phone) === 0) {
                throw ValidationException::withMessages([
                    'phone' => 'លេខទូរស័ព្ទមានក្នុងប្រព័ន្ធរួចហើយ។',
                ]);
            }
        }

        try {
            DB::beginTransaction();

            if ($request->has('photo')) {
                $photo_name = null;
                ImageService::removeProfileImage($student->photo, 'students');
                if (!empty($validated['photo'])) {
                    $photo_name = ImageService::storeProfileImage($validated['photo'], 'students');
                }
                $student->photo = $photo_name;
            }

            $student->name_kh = $validated['name_kh'];
            $student->name_en = strtoupper($validated['name_en']);
            $student->dob = $validated['dob'];
            $student->job = $validated['job'];
            $student->home_no = $validated['home_no'];
            $student->street_no = $validated['street_no'];
            $student->phone = $phone;
            $student->id_gender = $validated['id_gender'];
            $student->id_ethnicity = $validated['id_ethnicity'];
            $student->id_nationality = $validated['id_nationality'];
            $student->id_religion = $validated['id_religion'];
            $student->id_pob = $id_pob;
            $student->id_por = $id_por;

            $updated = $student->save();
            if (!$updated) {
                return ResponseHelper::updateErrorMsg();
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            throw $e;
        } catch (Throwable $th) {
            DB::rollback();
            throw $th;
        }

        return response(
            [
                'message' => 'The student has been updated.',
                'student' => new ManageStudentResource($student->load($this->manageEagerLoading)),
            ],
            200,
        );
    }

    public function deleteStudent($id_student)
    {
        $student = Student::where('id_student', $id_student)
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
                'student' => new ManageStudentResource($student),
            ],
            200,
        );
    }

    private function resolveGeography($id_province, $id_district, $id_commune, $id_village)
    {
        $id_geography = null;

        if ($id_province) {
            $province = Province::find($id_province);
            if (!$province) {
                return ResponseHelper::requirementErrorMsg();
            }
            $id_geography = $province->id_geography;
        }

        if ($id_district) {
            $district = $province->districts->find($id_district);
            if (!$district) {
                return ResponseHelper::requirementErrorMsg();
            }
            $id_geography = $district->id_geography;
        }

        if ($id_commune) {
            $commune = $district->communes->find($id_commune);
            if (!$commune) {
                return ResponseHelper::requirementErrorMsg();
            }
            $id_geography = $commune->id_geography;
        }

        if ($id_village) {
            $village = $commune->villages->find($id_village);
            if (!$village) {
                return ResponseHelper::requirementErrorMsg();
            }
            $id_geography = $village->id_geography;
        }

        return $id_geography;
    }
}
