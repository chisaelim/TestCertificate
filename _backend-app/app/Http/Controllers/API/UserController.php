<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\User\CreateUserRequest;
use App\Http\Requests\User\DeleteUserRequest;
use App\Http\Requests\User\GetUsersRequest;
use App\Http\Requests\User\ReadUserRequest;
use App\Http\Requests\User\UpdateUserRequest;
use App\Http\Resources\User\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function getUsers(GetUsersRequest $request)
    {
        $users = User::all();

        return response([
            'users' => UserResource::collection($users),
        ], 200);
    }

    public function createUser(CreateUserRequest $request)
    {
        try {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'level' => $request->level,
            ]);

            // Trusted-admin model: admin-created accounts are auto-verified.
            $user->markEmailAsVerified();
        } catch (Exception $e) {
            return ResponseHelper::createErrorMsg();
        }

        return response([
            'message' => 'The user has been created.',
            'user' => new UserResource($user),
        ], 201);
    }

    public function readUser(ReadUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFoundErrorMsg();
        }

        return response([
            'user' => new UserResource($user),
        ], 200);
    }

    public function updateUser(UpdateUserRequest $request)
    {
        $validated = $request->validated();
        $user = User::find($validated['id']);

        if (!$user) {
            return ResponseHelper::notFoundErrorMsg();
        }

        // Prevent downgrading the last administrator.
        if (
            $user->level === '_ADMINISTRATOR_'
        ) {
            return ResponseHelper::customErrorMsg('Can not update the administrator.', 409);
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->level = $validated['level'];

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
            // Invalidate existing sessions after password reset.
            $user->tokens()->delete();
        }

        try {
            $user->save();
        } catch (Exception $e) {
            return ResponseHelper::updateErrorMsg();
        }

        return response([
            'message' => 'The user has been updated.',
            'user' => new UserResource($user),
        ], 200);
    }

    public function deleteUser(DeleteUserRequest $request, $id)
    {
        $user = User::find($id);

        if (!$user) {
            return ResponseHelper::notFoundErrorMsg();
        }

        // Prevent self-deletion.
        if ((int) $id === $request->user()->id) {
            return ResponseHelper::customErrorMsg('You cannot delete your own account.', 409);
        }

        // Prevent deleting the last administrator.
        if ($user->level === '_ADMINISTRATOR_') {
            return ResponseHelper::customErrorMsg('Can not delete the administrator.', 409);
        }

        try {
            $user->tokens()->delete();
            $user->delete();
        } catch (Exception $e) {
            return ResponseHelper::deleteErrorMsg();
        }

        return response([
            'message' => 'The user has been deleted.',
            'user' => new UserResource($user),
        ], 200);
    }
}
