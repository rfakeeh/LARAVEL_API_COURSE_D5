<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\ImageController;
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

//Route::resource('category', CategoryController::class);
//Route::get('open_document', [CategoryController::class, 'open_document']);

// public api routes with api/ prefix
Route::get('category', [CategoryController::class, 'index']);
Route::get('category/{id}', [CategoryController::class, 'show']);
Route::get('news/', [NewsController::class, 'public_index']);
Route::get('news/{id}', [NewsController::class, 'public_show']);

// grouped api routes with api/user/ prefix 
Route::prefix('user')->group(function () {

    Route::post('register', [UserController::class, 'register']);
    Route::post('login', [UserController::class, 'login']);

    // passport auth api routes
    Route::middleware(['auth:api'])->group(function () {

        Route::get('/', [UserController::class, 'profile']);
        Route::get('logout', [UserController::class, 'logout']);

        Route::post('category', [CategoryController::class, 'store']);
        Route::put('category/{id}', [CategoryController::class, 'update']);
        Route::delete('category/{id}', [CategoryController::class, 'destroy']);

        Route::get('news', [NewsController::class, 'private_index']);
        Route::post('news', [NewsController::class, 'store']);
        Route::get('news/{id}', [NewsController::class, 'show']);
        Route::put('news/{id}', [NewsController::class, 'update']);
        Route::delete('news/{id}', [NewsController::class, 'destroy']);

        Route::post('album', [ImageController::class, 'store']);
        Route::delete('album/{id}', [ImageController::class, 'destroy']);
        
    });

});

/*
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
*/
