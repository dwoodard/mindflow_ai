<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect('/chat');
});

Route::get('/chat', function () {
    return Inertia::render('Chat');
});
