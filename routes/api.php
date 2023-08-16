<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Route::get('auth', [AuthController::class, 'redirectToAuth']);
// Route::get('auth/callback', [AuthController::class, 'handleAuthCallback']);

Route::get('/auth',[App\Http\Controllers\api\AuthController::class,'redirectToAuth']);
Route::get('auth/callback',[App\Http\Controllers\api\AuthController::class,'handleAuthCallback']);



Route::get('/instructor',[App\Http\Controllers\api\AuthController::class,'instructor']);
Route::get('/student',[App\Http\Controllers\api\AuthController::class,'student']);

  // user //

Route::post('register',[App\Http\Controllers\api\AuthController::class,'register']);
Route::post('login',[App\Http\Controllers\api\AuthController::class,'login']);
Route::post('newlogin',[App\Http\Controllers\api\AuthController::class,'newlogin']);
Route::post('/forgotPassword', [App\Http\Controllers\api\AuthController::class, 'forgotPassword']);
Route::post('/updatePassword', [App\Http\Controllers\api\AuthController::class, 'updatePassword']);


               //blogs  

Route::post('addBlog',[App\Http\Controllers\api\AdminAuthController::class,'addBlog']);
Route::get('blogGet',[App\Http\Controllers\api\AdminAuthController::class,'blogGet']);
Route::get('blogGet/{id}',[App\Http\Controllers\api\AdminAuthController::class,'show']);
Route::delete('blogDestroy/{id}',[App\Http\Controllers\api\AdminAuthController::class,'blogDestroy']);
Route::post('blogUpdate/{id}',[App\Http\Controllers\api\AdminAuthController::class,'update']);
Route::post('/otp/verify', [App\Http\Controllers\api\AuthController::class, 'otpVerification']);



    ///  contact
Route::apiResource('contacts', App\Http\Controllers\api\ContactController::class);




Route::middleware('auth:api')->group(function () {
Route::post('/PasswordChanged ', [App\Http\Controllers\api\AuthController::class, 'PasswordChanged']);
Route::post('/update/AdminProfile', [App\Http\Controllers\api\AdminAuthController::class, 'adminProfile']);
Route::get('/logout',[App\Http\Controllers\api\AuthController::class,'logout']);
Route::get('AllUser',[App\Http\Controllers\api\MessageController::class,'AllUser']);
Route::put('/update/profile', [App\Http\Controllers\api\AuthController::class, 'updateProfile']);





});
