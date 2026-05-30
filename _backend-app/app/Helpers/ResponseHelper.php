<?php

namespace App\Helpers;

class ResponseHelper
{
    public static function unauthenticatedErrorMsg()
    {
        return abort(response([
            'message' => 'You are not authenticated! please login!',
        ], 401));
    }
    public static function unauthorizedErrorMsg()
    {
        return abort(response([
            'message' => 'You are not permitted to perform the action!',
        ], 417));
    }

    public static function requirementErrorMsg()
    {
        return abort(response([
            'message' => 'The provided information is not valid!',
        ], 417));
    }

    public static function createErrorMsg()
    {
        return abort(response([
            'message' => 'The record can not be created!',
        ], 422));
    }
    public static function updateErrorMsg()
    {
        return abort(response([
            'message' => 'The record can not be updated!',
        ], 422));
    }

    public static function deleteErrorMsg()
    {
        return abort(response([
            'message' => 'The record can not be deleted!',
        ], 422));
    }

    public static function notFoundErrorMsg()
    {
        return abort(response([
            'message' => 'The record was not found!',
        ], 417));
    }

    public static function forbiddenErrorMsg()
    {
        return abort(response([
            'message' => 'You do not have permission to access this resource.',
        ], 403));
    }

    public static function customErrorMsg($message = 'SERVER ERROR', $code = 500)
    {
        return abort(response(['message' => $message], $code));
    }
}
