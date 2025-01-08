<?php

use App\Http\Controllers\ChatbotController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Home'); // Renders Home.vue
});

Route::get('/about', function () {
    return Inertia::render('About'); // Renders About.vue
});

Route::get('/chat', [ChatbotController::class, 'index']);

Route::get('/chat', function () {
    return Inertia::render('Chat');
});
