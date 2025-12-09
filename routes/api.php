<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\SectionController;

// Public API routes
Route::get('/projects/public', [ProjectController::class, 'public']);
Route::get('/skills/public', [SkillController::class, 'public']);
Route::get('/sections', [SectionController::class, 'public']);
Route::post('/contact', [MessageController::class, 'store']);

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);
    Route::get('/dashboard/activities', [DashboardController::class, 'activities']);
    
    // Projects
    Route::apiResource('projects', ProjectController::class);
    
    // Skills
    Route::apiResource('skills', SkillController::class);
    
    // Messages
    Route::get('/messages', [MessageController::class, 'index']);
    Route::post('/messages/{id}/read', [MessageController::class, 'markAsRead']);
    Route::delete('/messages/{id}', [MessageController::class, 'destroy']);
});
