<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\OllamaController;

Route::get('/', function () {
    return redirect('/chat');
});

Route::get('/chat', function () {
    return Inertia::render('Chat');
});

Route::post('/generate', [OllamaController::class, 'generate']);
