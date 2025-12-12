<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\TaskController;
use App\Http\Controllers\Api\QuestionController;
use App\Http\Controllers\Api\ProblemController;
use App\Http\Controllers\Api\TeamController;
use App\Http\Controllers\Api\TEFController;
use App\Http\Controllers\Api\WebhookController;
use Illuminate\Support\Facades\Route;

// Health check
Route::get('/health', fn() => response()->json(['status' => 'ok']));

// Auth routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
    Route::get('/user', [AuthController::class, 'user'])->middleware('auth:sanctum');
});

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/activity', [DashboardController::class, 'activity']);
    
    // My Tasks (cross-project)
    Route::get('/my/tasks', [TaskController::class, 'myTasks']);
    Route::get('/my/requests', [TaskController::class, 'myRequests']);

    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::get('/projects/{project}/stats', [ProjectController::class, 'stats']);
    
    // Project Members
    Route::get('/projects/{project}/members', [TeamController::class, 'index']);
    Route::post('/projects/{project}/members', [TeamController::class, 'store']);
    Route::put('/projects/{project}/members/{member}', [TeamController::class, 'update']);
    Route::delete('/projects/{project}/members/{member}', [TeamController::class, 'destroy']);

    // Tasks (nested under projects)
    Route::prefix('projects/{project}')->group(function () {
        Route::apiResource('tasks', TaskController::class);
        
        // Task state transitions
        Route::post('/tasks/{task}/accept', [TaskController::class, 'accept']);
        Route::post('/tasks/{task}/decline', [TaskController::class, 'decline']);
        Route::post('/tasks/{task}/start', [TaskController::class, 'start']);
        Route::post('/tasks/{task}/complete', [TaskController::class, 'complete']);
        Route::post('/tasks/{task}/cancel', [TaskController::class, 'cancel']);
        
        // Task messages
        Route::get('/tasks/{task}/messages', [TaskController::class, 'messages']);
        Route::post('/tasks/{task}/messages', [TaskController::class, 'addMessage']);
        
        // Task TEF export
        Route::get('/tasks/{task}/tef', [TEFController::class, 'exportTask']);
    });
    
    // Questions
    Route::prefix('projects/{project}')->group(function () {
        Route::apiResource('questions', QuestionController::class);
        Route::post('/questions/{question}/vote', [QuestionController::class, 'vote']);
        Route::post('/questions/{question}/convert', [QuestionController::class, 'convertToTask']);
        
        // Answers
        Route::post('/questions/{question}/answers', [QuestionController::class, 'addAnswer']);
        Route::post('/questions/{question}/answers/{answer}/accept', [QuestionController::class, 'acceptAnswer']);
        Route::post('/answers/{answer}/vote', [QuestionController::class, 'voteAnswer']);
    });
    
    // Problems
    Route::prefix('projects/{project}')->group(function () {
        Route::apiResource('problems', ProblemController::class);
        Route::post('/problems/{problem}/resolve', [ProblemController::class, 'resolve']);
    });
    
    // TEF Import/Export
    Route::get('/projects/{project}/tef', [TEFController::class, 'exportProject']);
    Route::post('/projects/{project}/tef/import', [TEFController::class, 'importTasks']);
});

// Webhooks (no auth - signature verification instead)
Route::prefix('webhooks')->group(function () {
    Route::post('/email', [WebhookController::class, 'email']);
    Route::post('/sms', [WebhookController::class, 'sms']);
    Route::post('/voice', [WebhookController::class, 'voice']);
    Route::post('/slack', [WebhookController::class, 'slack']);
});

