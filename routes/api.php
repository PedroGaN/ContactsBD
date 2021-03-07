<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserController;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/cards/searchCard',[CardController::class,"searchCard"]);

Route::group(['middleware' => 'auth:api'], function(){

    Route::post('/users/logout',[UserController::class,"logoutUser"]); 
    Route::post('/users/update',[UserController::class,"updateUser"]); 
    Route::post('/users/contacts',[UserController::class,"addContacts"]); 
    Route::post('/users/delete',[UserController::class,"deleteUser"]);
    Route::get('/users/fetch',[UserController::class,"fetchUsers"]);
});

Route::prefix('users')->group(function () {
    Route::post('/register',[UserController::class,"createUser"]);
    Route::post('/login',[UserController::class,"loginUser"]);
    //Route::post('/forgotPassword',[UserController::class,"recoverPassword"]);
});