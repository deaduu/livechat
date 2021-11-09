<?php

use Deaduu\Livechat\Http\Controllers\ChatController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'livechat', 'middleware' => ['web', 'auth']], function () {

    Route::get('chat', [ChatController::class, 'index']);
    Route::get('/chatbox', [ChatController::class, 'chat']);
    Route::get('/chatbox/{thread}', [ChatController::class, 'thread']);
    Route::get('/fetch/{thread}', [ChatController::class, 'fetchdata']);
    Route::post('/sendmessage', [ChatController::class, 'sendmessage']);
    Route::get('/users', [ChatController::class, 'users']);
});
