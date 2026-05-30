<?php

namespace App\Services;

use App\Classes\ImageClass;

class ImageClassService
{
    public static function forUserModel(): ImageClass
    {
        return new ImageClass('public', 'users/profile-images', 128);
    }

    public static function forStudentModel(): ImageClass
    {
        return new ImageClass('public', 'students/images', 128);
    }
}
