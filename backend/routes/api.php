<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// Rotas públicas (sem autenticação necessária por enquanto)
Route::prefix('v1')->group(function () {
    
    // Exames
    Route::apiResource('exams', App\Http\Controllers\Api\ExamController::class);
    Route::get('exams/{id}/questions', [App\Http\Controllers\Api\ExamController::class, 'questions']);
    
    // Categorias
    Route::apiResource('categories', App\Http\Controllers\Api\CategoryController::class);
    Route::get('categories/{id}/questions', [App\Http\Controllers\Api\CategoryController::class, 'questions']);
    
    // Questões
    Route::apiResource('questions', App\Http\Controllers\Api\QuestionController::class);
    Route::post('questions/{id}/answer', [App\Http\Controllers\Api\QuestionController::class, 'answer']);
    
    // Progresso do usuário
    Route::get('user/progress', [App\Http\Controllers\Api\UserProgressController::class, 'index']);
    Route::get('user/statistics', [App\Http\Controllers\Api\UserProgressController::class, 'statistics']);
    Route::get('user/progress/{id}', [App\Http\Controllers\Api\UserProgressController::class, 'show']);
    Route::delete('user/progress/{id}', [App\Http\Controllers\Api\UserProgressController::class, 'destroy']);
});
