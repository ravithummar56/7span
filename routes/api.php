<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('login', [AuthController::class, 'login']);
Route::group(['prefix' => 'users'], function()  
{  
    // we can use resource rout but we have image in request so put and patch is use url-encoed so not possible 
   Route::get('/',[UserController::class, 'index']);
   Route::post('/',[UserController::class, 'store']);
   Route::get('/{id}',[UserController::class, 'show']);
   Route::post('/{id}',[UserController::class, 'update']);
   Route::delete('/{id}',[UserController::class, 'destroy']);
});  

Route::middleware('auth:sanctum')->group( function () {
   // update hobby
   Route::post('update-hobby',[UserController::class, 'updateHobby']);
   Route::get('list-user-by-filter',[UserController::class, 'listUserByAdmin']);
});
