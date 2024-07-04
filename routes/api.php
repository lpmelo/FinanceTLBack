<?php

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

Route::apiResource('users', 'App\Http\Controllers\UsersController');

Route::apiResource('auth', 'App\Http\Controllers\AuthenticationController');

Route::apiResource('monetary', "App\Http\Controllers\MonetaryController");
