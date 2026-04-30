<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::get('/chat', function () {
    return view('chat');
});

Route::post('/chat', [ChatController::class, 'send']);