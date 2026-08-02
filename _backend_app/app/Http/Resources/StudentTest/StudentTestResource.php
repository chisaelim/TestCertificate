<?php

namespace App\Http\Resources\StudentTest;

use App\Http\Resources\Student\StudentResource;
use App\Http\Resources\Test\TestResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StudentTestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'issued_date' => $this->issued_date,
            'expired_date' => $this->expired_date,
            'status' => $this->status,
            'student' => $this->whenLoaded('student', fn() => new StudentResource($this->student)),
            'test' => $this->whenLoaded('test', fn() => new TestResource($this->test)),
        ];
    }
}
