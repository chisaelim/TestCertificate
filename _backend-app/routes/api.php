<?php

use App\Http\Controllers\API\AssetController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\GeographyController;
use App\Http\Controllers\API\GoogleOAuthController;
use App\Http\Controllers\API\StudentController;
use Illuminate\Support\Facades\Route;

Route::post('/signup', [AuthController::class, 'signup']);
Route::post('/signin', [AuthController::class, 'signin']);
Route::get('/verify/email/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware('signed')
    ->name('verify.email');
Route::post('/send/verification-email', [AuthController::class, 'sendVerificationEmail']);
Route::post('/send/reset-password-email', [AuthController::class, 'sendResetPasswordEmail']);
Route::post('/set/new-password', [AuthController::class, 'setNewPassword'])->name('set.new-password');

Route::prefix('google')->group(function () {
    Route::get('/oauth/redirect', [GoogleOAuthController::class, 'googleOAuthRedirect']);
    Route::get('/oauth/callback', [GoogleOAuthController::class, 'googleOAuthCallback']);
    Route::post('/oauth/exchange/token', [GoogleOAuthController::class, 'googleOAuthExchangeToken'])->middleware('auth:sanctum');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/signout', [AuthController::class, 'signout']);
    Route::get('/verify', [AuthController::class, 'verify']);
    Route::put('/create/password', [AuthController::class, 'createPassword']);
    Route::put('/change/password', [AuthController::class, 'changePassword']);
    Route::put('/update/profile-image', [AuthController::class, 'updateProfileImage']);
    Route::delete('/delete/profile-image', [AuthController::class, 'deleteProfileImage']);




    Route::prefix('/assets')->group(function () {
        Route::get('/all/genders', [AssetController::class, 'getAllGenders']);
        Route::get('/all/nationalities', [AssetController::class, 'getAllNationalities']);
        Route::get('/all/ethnicities', [AssetController::class, 'getAllEthnicities']);
        Route::get('/all/religions', [AssetController::class, 'getAllReligions']);
    });

    Route::get('/provinces', [GeographyController::class, 'getProvinces']);
    Route::get('/districts/by/province/{id}', [GeographyController::class, 'getDistrictsByProvinceID']);
    Route::get('/communes/by/district/{id}', [GeographyController::class, 'getCommunesByDistrictID']);
    Route::get('/villages/by/commune/{id}', [GeographyController::class, 'getVillagesByCommuneID']);

    Route::middleware('_ADMINISTRATOR_')->group(function () {
        Route::prefix('/provinces')->group(function () {
            Route::get('/read/{id_province}', [GeographyController::class, 'readProvince']);
            Route::post('/create', [GeographyController::class, 'createProvince']);
            Route::put('/update', [GeographyController::class, 'updateProvince']);
            Route::delete('/delete/{id_province}', [GeographyController::class, 'deleteProvince']);
        });
        Route::prefix('/communes')->group(function () {
            Route::get('/read/{id}', [GeographyController::class, 'readCommune']);
            Route::post('/create', [GeographyController::class, 'createCommune']);
            Route::put('/update', [GeographyController::class, 'updateCommune']);
            Route::delete('/delete/{id}', [GeographyController::class, 'deleteCommune']);
        });
        Route::prefix('/districts')->group(function () {
            Route::get('/read/{id}', [GeographyController::class, 'readDistrict']);
            Route::post('/create', [GeographyController::class, 'createDistrict']);
            Route::put('/update', [GeographyController::class, 'updateDistrict']);
            Route::delete('/delete/{id}', [GeographyController::class, 'deleteDistrict']);
        });
        Route::prefix('/villages')->group(function () {
            Route::get('/read/{id}', [GeographyController::class, 'readVillage']);
            Route::post('/create', [GeographyController::class, 'createVillage']);
            Route::put('/update', [GeographyController::class, 'updateVillage']);
            Route::delete('/delete/{id}', [GeographyController::class, 'deleteVillage']);
        });
    });

    Route::prefix('/students')->group(function () {
        Route::get('/', [StudentController::class, 'getStudents']);
        Route::get('/details', [StudentController::class, 'getStudentsWithDetails']);
        Route::get('/read/{id_student}', [StudentController::class, 'readStudent']);
        Route::post('/create', [StudentController::class, 'createStudent']);
        Route::put('/update', [StudentController::class, 'updateStudent']);
        Route::delete('/delete/{id_student}', [StudentController::class, 'deleteStudent']);
    });
});
