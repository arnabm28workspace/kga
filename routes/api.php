<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\TestController;
use App\Http\Controllers\Api\Staff\AuthController;
use App\Http\Controllers\Api\Staff\ScanController;
use App\Http\Controllers\Api\CartController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

# Testing purpose
Route::prefix('test')->name('test.')->group(function(){
    Route::get('/index', [TestController::class, 'index'])->name('index');
    Route::get('/success', [TestController::class, 'success'])->name('success');
    Route::get('/error', [TestController::class, 'error'])->name('error');
    Route::post('/save', [TestController::class, 'save'])->name('save');
    Route::get('/create-token-by-userid', [TestController::class, 'create_token_by_userid'])->name('create-token-by-userid');
});
