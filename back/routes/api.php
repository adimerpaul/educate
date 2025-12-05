<?php

use App\Http\Controllers\GoogleController;
use App\Http\Controllers\TareaController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::get('/auth/google/redirect', [GoogleController::class, 'redirect']);
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);
//funcion api sanctum middleware

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [App\Http\Controllers\UserController::class, 'logout']);
    Route::get('/me', [App\Http\Controllers\UserController::class, 'me']);

    Route::apiResource('materias', App\Http\Controllers\MateriaController::class);
    Route::apiResource('tareas', App\Http\Controllers\TareaController::class);

    // PERFIL
    Route::get('/profile/me', [App\Http\Controllers\ProfileController::class, 'me']);
    Route::post('/profile/save', [App\Http\Controllers\ProfileController::class, 'save']);

    // HORARIOS DE ESTUDIO
    Route::get('/study-schedules', [App\Http\Controllers\StudyScheduleController::class, 'index']);
    Route::post('/study-schedules', [App\Http\Controllers\StudyScheduleController::class, 'store']);
    Route::put('/study-schedules/{id}', [App\Http\Controllers\StudyScheduleController::class, 'update']);
    Route::delete('/study-schedules/{id}', [App\Http\Controllers\StudyScheduleController::class, 'destroy']);

    // COMPLETAR ONBOARDING
    Route::post('/onboarding/complete', [App\Http\Controllers\UserController::class, 'completeOnboarding']);
});
