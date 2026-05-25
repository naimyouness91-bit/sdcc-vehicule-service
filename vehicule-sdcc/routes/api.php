<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\Api\ServiceApiController;

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

// Public service endpoints (no auth required)
Route::get('/services/grouped', [ServiceApiController::class, 'grouped'])->name('api.services.grouped');
Route::get('/services/departments', [ServiceApiController::class, 'departments'])->name('api.services.departments');
Route::get('/services/department/{department}', [ServiceApiController::class, 'byDepartment'])->name('api.services.by-department');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Protected vehicle endpoints
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cars/available-by-date', [CarController::class, 'getAvailableByDate']);
    Route::get('/cars/available', [CarController::class, 'available'])->name('api.cars.available');
    Route::patch('/cars/{id}/availability', [CarController::class, 'updateAvailability'])->name('api.cars.update-availability');
});
