<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\OllamaController;
use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/generate', [OllamaController::class, 'generate']);

Route::prefix('agent')->group(function () {
    // Trigger an agent workflow (e.g., PromptChain, Router, etc.)
    Route::post('execute', [AgentController::class, 'execute'])
        ->name('agent.execute');

    // Trigger a specific pattern (e.g., PromptChain)
    Route::post('execute/prompt-chain', [AgentController::class, 'executePromptChain'])
        ->name('agent.execute.prompt-chain');

    // Trigger another pattern (e.g., Orchestrator)
    Route::post('execute/orchestrator', [AgentController::class, 'executeOrchestrator'])
        ->name('agent.execute.orchestrator');
});

Route::prefix('tasks')->group(function () {
    // Create a new task
    Route::post('/', [TaskController::class, 'store'])
        ->name('tasks.store');

    // Get the status of a task
    Route::get('{task}', [TaskController::class, 'show'])
        ->name('tasks.show');

    // List all tasks
    Route::get('/', [TaskController::class, 'index'])
        ->name('tasks.index');

    // Delete a task
    Route::delete('{task}', [TaskController::class, 'destroy'])
        ->name('tasks.destroy');
});
