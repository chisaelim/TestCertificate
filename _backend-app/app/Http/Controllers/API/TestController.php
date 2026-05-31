<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Resources\Test\DetailTestResource;
use App\Http\Resources\Test\TestResource;
use Exception;
use App\Models\Test;
use App\Http\Controllers\Controller;
use App\Http\Requests\Test\GetTestsRequest;
use App\Http\Requests\Test\GetTestsWithDetailsRequest;
use App\Http\Requests\Test\CreateTestRequest;
use App\Http\Requests\Test\UpdateTestRequest;
use App\Http\Requests\Test\ReadTestRequest;
use App\Http\Requests\Test\DeleteTestRequest;

class TestController extends Controller
{
    private $manageEagerLoading = [
        'creator',
        'updater',
    ];

    public function getTests(GetTestsRequest $request)
    {
        $tests = Test::all();

        return response(
            [
                'tests' => TestResource::collection($tests),
            ],
            200
        );
    }

    public function getTestsWithDetails(GetTestsWithDetailsRequest $request)
    {
        $tests = Test::with($this->manageEagerLoading)->get();

        return response(
            [
                'tests' => DetailTestResource::collection($tests),
            ],
            200,
        );
    }

    public function createTest(CreateTestRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $name_kh = $validated['name_kh'];
        $name_en = $validated['name_en'];
        $short_name = $validated['short_name'];

        try {
            $test = Test::create([
                'name_kh' => $name_kh,
                'name_en' => $name_en,
                'short_name' => $short_name,
            ]);

            $test = Test::where('id', $test->id)
                ->with($this->manageEagerLoading)
                ->first();
        } catch (Exception $e) {
            return ResponseHelper::createErrorMsg();
        }

        return response(
            [
                'message' => 'The test has been created.',
                'test' => new DetailTestResource($test),
            ],
            201,
        );
    }

    public function updateTest(UpdateTestRequest $request)
    {
        $user = $request->user();
        $validated = $request->validated();

        $id = $validated['id'];
        $name_kh = $validated['name_kh'];
        $name_en = $validated['name_en'];
        $short_name = $validated['short_name'];

        try {
            $test = Test::find($id);
            $test->name_kh = $name_kh;
            $test->name_en = $name_en;
            $test->short_name = $short_name;
            $updated = $test->save();
            if (!$updated) {
                return ResponseHelper::updateErrorMsg();
            }

            $test = Test::where('id', $test->id)
                ->with($this->manageEagerLoading)
                ->first();
        } catch (Exception $e) {
            return ResponseHelper::updateErrorMsg();
        }

        return response(
            [
                'message' => 'The test has been updated.',
                'test' => new DetailTestResource($test),
            ],
            200,
        );
    }

    public function readTest(ReadTestRequest $request, $id)
    {
        $test = Test::where('id', $id)
            ->with($this->manageEagerLoading)
            ->first();

        if (!$test) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response(
            [
                'test' => new DetailTestResource($test),
            ],
            200,
        );
    }

    public function deleteTest(DeleteTestRequest $request, $id)
    {
        $test = Test::where('id', $id)
            ->with($this->manageEagerLoading)
            ->first();

        if (!$test) {
            return ResponseHelper::notFoundErrorMsg();
        }

        try {
            $test->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response(
            [
                'message' => 'The test has been deleted.',
                'test' => new DetailTestResource($test),
            ],
            200,
        );
    }
}
