<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\DataController;

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

Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout']);

Route::middleware('auth.token')->group(function () {
    Route::get('/divisions', [DataController::class, 'divisi']);
    Route::get('/pegawai', [DataController::class, 'pegawai']);
    Route::post('/create', [DataController::class, 'create']);
    Route::put('/update/{id}', [DataController::class, 'update']);
    Route::delete('/destroy/{id}', [DataController::class, 'destroy']);
});