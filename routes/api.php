<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InformasiController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PemasokController;
use App\Http\Controllers\Api\ProdukController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/auth/login', [AuthController::class, 'login']);
Route::middleware(['jwt.auth'])->get('/auth/check', [AuthController::class, 'check']);


Route::middleware(['jwt.auth'])->apiResource('produk', ProdukController::class);
Route::middleware(['jwt.auth'])->apiResource('pemasok', PemasokController::class);
Route::middleware(['jwt.auth'])->apiResource('pelanggan', PelangganController::class);
Route::middleware(['jwt.auth'])->apiResource('informasi', InformasiController::class);
