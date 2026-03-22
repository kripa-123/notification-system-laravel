<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\NotificationController;


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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::get('/dev-token', function () {
    return ['token' => \App\Models\User::first()->createToken('demo')->plainTextToken];
});

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/notifications', [NotificationController::class, 'store']);
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/summary', [NotificationController::class, 'summary']);
});



