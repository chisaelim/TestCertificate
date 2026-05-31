<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\StudentTest\GetStudentTestsByGeographyRequest;
use Carbon\Carbon;
use Exception;
use App\Models\StudentTest;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\StudentTest\StudentTestResource;
use App\Http\Resources\StudentTest\DetailStudentTestResource;
use App\Http\Requests\StudentTest\GetStudentTestsByStudentIdRequest;
use App\Http\Requests\StudentTest\GetStudentTestsByIssuedDateRequest;
use App\Http\Requests\StudentTest\CreateStudentTestRequest;
use App\Http\Requests\StudentTest\UpdateStudentTestRequest;
use App\Http\Requests\StudentTest\ReadStudentTestRequest;
use App\Http\Requests\StudentTest\DeleteStudentTestRequest;
use App\Http\Requests\StudentTest\ChangeStudentTestStatusRequest;
use App\Http\Requests\StudentTest\GetPassedStudentTestsForCertificatesRequest;
use Illuminate\Validation\ValidationException;

class StudentTestController extends Controller
{
    private $manageEagerLoading = [
        'test',
        'student.gender',
        'creator',
        'updater',
    ];

    private $certificateEagerLoading = [
        'test',
        'student.gender',
        'student.placeOfBirth.parent.parent.parent',
        'student.placeOfResidence.parent.parent.parent',
    ];

    public function getStudentTestsByStudent(GetStudentTestsByStudentIdRequest $request, $id)
    {
        $student_tests = StudentTest::where('student_id', $id)

            ->get();

        return response(
            [
                'student_tests' => StudentTestResource::collection($student_tests),
            ],
            200,
        );
    }
    public function getStudentTestsWithDetailsByStudent(GetStudentTestsByStudentIdRequest $request, $id)
    {
        $student_tests = StudentTest::where('student_id', $id)
            ->with($this->manageEagerLoading)
            ->get();

        return response(
            [
                'student_tests' => DetailStudentTestResource::collection($student_tests),
            ],
            200,
        );
    }
    public function getStudentTestsByIssuedDate(GetStudentTestsByIssuedDateRequest $request, $issued_date)
    {
        $student_tests = StudentTest::where('issued_date', Carbon::createFromFormat('d-m-Y', $issued_date)->format('Y-m-d'))
            ->get();

        return response(
            [
                'student_tests' => StudentTestResource::collection($student_tests),
            ],
            200,
        );
    }
    public function getStudentTestsWithDetailsByIssuedDate(GetStudentTestsByIssuedDateRequest $request, $issued_date)
    {
        $student_tests = StudentTest::where('issued_date', Carbon::createFromFormat('d-m-Y', $issued_date)->format('Y-m-d'))
            ->with($this->manageEagerLoading)
            ->get();

        return response(
            [
                'student_tests' => DetailStudentTestResource::collection($student_tests),
            ],
            200,
        );
    }
    public function createStudentTest(CreateStudentTestRequest $request)
    {
        $validated = $request->validated();

        $student_id = $validated['student_id'];
        $test_id = $validated['test_id'];
        $issued_date = Carbon::createFromFormat('d-m-Y', $validated['issued_date'])->format('Y-m-d');
        $expired_date = Carbon::createFromFormat('d-m-Y', $validated['issued_date'])->addYears(5)->format('Y-m-d');

        $existed = StudentTest::where('student_id', $student_id)
            ->where('test_id', $test_id)
            ->where('issued_date', $issued_date)
            ->first();
        if ($existed) {
            throw ValidationException::withMessages([
                'issued_date' => 'ទិន្នន័យការធ្វើតេស្តមានរួចហើយ។',
            ]);
        }

        try {
            $student_test = StudentTest::create([
                'student_id' => $student_id,
                'test_id' => $test_id,
                'issued_date' => $issued_date,
                'expired_date' => $expired_date,
                'status' => 'PENDING',
            ]);

            $student_test = StudentTest::where('id', $student_test->id)
                ->with($this->manageEagerLoading)
                ->first();
        } catch (Exception $e) {
            return ResponseHelper::createErrorMsg();
        }

        return response(
            [
                'message' => 'The student test has been created.',
                'student_test' => new DetailStudentTestResource($student_test),
            ],
            201,
        );
    }
    public function updateStudentTest(UpdateStudentTestRequest $request)
    {
        $validated = $request->validated();

        $id = $validated['id'];
        $student_id = $validated['student_id'];
        $test_id = $validated['test_id'];
        $issued_date = Carbon::createFromFormat('d-m-Y', $validated['issued_date'])->format('Y-m-d');
        $expired_date = Carbon::createFromFormat('d-m-Y', $validated['issued_date'])->addYears(5)->format('Y-m-d');

        $existed = StudentTest::where('student_id', $student_id)
            ->where('test_id', $test_id)
            ->where('issued_date', $issued_date)
            ->first();
        if ($existed && $existed->id !== $id) {
            throw ValidationException::withMessages([
                'issued_date' => 'ទិន្នន័យការធ្វើតេស្តមានរួចហើយ។',
            ]);
        }

        try {
            $student_test = StudentTest::find($id);
            $student_test->test_id = $test_id;
            $student_test->issued_date = $issued_date;
            $student_test->expired_date = $expired_date;
            $updated = $student_test->save();
            if (!$updated) {
                return ResponseHelper::updateErrorMsg();
            }

            $student_test = StudentTest::where('id', $student_test->id)
                ->with($this->manageEagerLoading)
                ->first();
        } catch (Exception $e) {
            return ResponseHelper::updateErrorMsg();
        }

        return response(
            [
                'message' => 'The student test has been updated.',
                'student_test' => new DetailStudentTestResource($student_test),
            ],
            200,
        );
    }
    public function readStudentTest(ReadStudentTestRequest $request, $id)
    {
        $student_test = StudentTest::where('id', $id)
            ->with($this->manageEagerLoading)
            ->first();

        if (!$student_test) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'student_test' => new DetailStudentTestResource($student_test),
            ],
            200,
        );
    }
    public function deleteStudentTest(DeleteStudentTestRequest $request, $id)
    {
        $student_test = StudentTest::where('id', $id)
            ->with($this->manageEagerLoading)
            ->first();

        if (!$student_test) {
            return ResponseHelper::notFoundErrorMsg();
        }

        try {
            $student_test->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response(
            [
                'message' => 'The student test has been deleted.',
                'student_test' => new DetailStudentTestResource($student_test),
            ],
            200,
        );
    }
    public function changeStudentTestStatus(ChangeStudentTestStatusRequest $request)
    {
        $validated = $request->validated();

        $id = $validated['id'];
        $status = $validated['status'];

        $student_test = StudentTest::where('id', $id)
            ->with($this->manageEagerLoading)
            ->first();

        if (!$student_test) {
            return ResponseHelper::notFoundErrorMsg();
        }

        try {
            $student_test->status = $status;
            $updated = $student_test->save();
            if (!$updated) {
                return ResponseHelper::updateErrorMsg();
            }
        } catch (Exception $e) {
            return ResponseHelper::updateErrorMsg();
        }

        return response(
            [
                'message' => 'The student test status has been changed.',
                'student_test' => new DetailStudentTestResource($student_test),
            ],
            200,
        );
    }
    public function getPassedStudentTestsForCertificates(GetPassedStudentTestsForCertificatesRequest $request)
    {
        $validated = $request->validated();
        $passed_ids = $validated['passed_ids'];

        $student_tests = StudentTest::whereIn('id', $passed_ids)
            ->with($this->certificateEagerLoading)
            ->get();

        return response(
            [
                'student_tests' => DetailStudentTestResource::collection($student_tests),
            ],
            200,
        );
    }

    public function getStudentTestsByGeography(GetStudentTestsByGeographyRequest $request, $id)
    {
        $student_tests = StudentTest::whereHas('student.placeOfBirth', function ($q) use ($id) {
            $q->where('id', $id)
                ->orWhereHas('parent', function ($q) use ($id) {
                    $q->where('id', $id)
                        ->orWhereHas('parent', function ($q) use ($id) {
                            $q->where('id', $id)
                                ->orWhereHas('parent', function ($q) use ($id) {
                                    $q->where('id', $id);
                                });
                        });
                });
        })
            ->with($this->manageEagerLoading)
            ->get();

        return response(
            [
                'student_tests' => DetailStudentTestResource::collection($student_tests),
            ],
            200,
        );
    }
}
