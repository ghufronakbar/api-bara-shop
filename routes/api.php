<?php

use App\Http\Controllers\Api\AkunController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CacatProdukController;
use App\Http\Controllers\Api\InformasiController;
use App\Http\Controllers\Api\LogAksiController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PemasokController;
use App\Http\Controllers\Api\PembelianProdukController;
use App\Http\Controllers\Api\PenggunaController;
use App\Http\Controllers\Api\PesanController;
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

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::middleware(['jwt.auth'])->get('/check', [AuthController::class, 'check']);
});

Route::group(['middleware' => ['jwt.auth']], function () {
    Route::apiResource('produk', ProdukController::class);
    Route::apiResource('pemasok', PemasokController::class);
    Route::apiResource('pelanggan', PelangganController::class);
    Route::apiResource('informasi', InformasiController::class);
    Route::apiResource('cacat-produk', CacatProdukController::class);
    Route::apiResource('pembelian-produk', PembelianProdukController::class);
    Route::apiResource('pengguna', PenggunaController::class);
    Route::apiResource('pesan', PesanController::class);
    Route::apiResource('log-aksi', LogAksiController::class);
    Route::group(['prefix' => 'akun'], function () {
        Route::get('/', [AkunController::class, 'index']);
        Route::put('/', [AkunController::class, 'editProfile']);
        Route::patch('/', [AkunController::class, 'editPassword']);
    });
});
