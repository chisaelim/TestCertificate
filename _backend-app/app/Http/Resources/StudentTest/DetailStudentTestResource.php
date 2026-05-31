<?php

namespace App\Http\Resources\StudentTest;

use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\Test\TestResource;
use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailStudentTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'issued_date' => $this->issued_date,
            'expired_date' => $this->expired_date,
            'status' => $this->status,
            'student_id' => $this->student_id,
            'test_id' => $this->test_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'student' => $this->whenLoaded('student', fn() => new StudentResource($this->student)),
            'test' => $this->whenLoaded('test', fn() => new TestResource($this->test)),
            'creator' => $this->whenLoaded('creator', fn() => new UserResource($this->creator)),
            'updater' => $this->whenLoaded('updater', fn() => new UserResource($this->updater)),
        ];
    }
}
