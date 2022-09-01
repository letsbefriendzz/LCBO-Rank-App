<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::prefix('alcohol')->group(function () {
    Route::get('/efficient', 'App\Http\Controllers\AlcoholController@getEfficient')->middleware(['auth:sanctum']);
    Route::get('/', 'App\Http\Controllers\AlcoholController@getDefault')->middleware(['auth:sanctum']);
});

Route::post('/tokens/create', function (Request $request) { // TODO create controller
    $token = $request->user()->createToken($request->token_name);
    return ['token' => $token->plainTextToken];
});
