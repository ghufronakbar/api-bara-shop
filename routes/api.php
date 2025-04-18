<?php

use App\Http\Controllers\Api\AkunController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CacatProdukController;
use App\Http\Controllers\Api\ImageController;
use App\Http\Controllers\Api\InformasiController;
use App\Http\Controllers\Api\LaporanController;
use App\Http\Controllers\Api\LogAksiController;
use App\Http\Controllers\Api\PelangganController;
use App\Http\Controllers\Api\PemasokController;
use App\Http\Controllers\Api\PembelianProdukController;
use App\Http\Controllers\Api\PenggunaController;
use App\Http\Controllers\Api\PeranController;
use App\Http\Controllers\Api\PesananController;
use App\Http\Controllers\Api\PesanController;
use App\Http\Controllers\Api\ProdukController;
use App\Http\Controllers\Api\RingkasanController;
use Illuminate\Support\Facades\Route;

Route::group(['prefix' => 'auth'], function () {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    Route::middleware(['jwt.auth'])->get('/check', [AuthController::class, 'check']);
});

Route::post('image', [ImageController::class, 'upload']);

Route::group(['prefix' => 'laporan'], function () {
    Route::get('/', [LaporanController::class, 'index']);
    Route::get('/penjualan', [LaporanController::class, 'laporanPenjualan']);
    Route::get('/pembelian', [LaporanController::class, 'laporanPembelian']);
    Route::get('/kerusakan', [LaporanController::class, 'laporanKerusakan']);
    Route::get('/produk', [LaporanController::class, 'laporanProduk']);
    Route::get('/pelanggan', [LaporanController::class, 'laporanPelanggan']);
    Route::get('/pemasok', [LaporanController::class, 'laporanPemasok']);
});

Route::get('/ringkasan', [RingkasanController::class, 'index']);

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
    Route::apiResource('peran', PeranController::class);
    Route::group(['prefix' => 'pesanan'], function () {
        Route::get('/', [PesananController::class, 'index']);
        Route::post('/', [PesananController::class, 'store']);
        Route::get('/{id}', [PesananController::class, 'show']);
        Route::post('/nota', [PesananController::class, 'kirimNota']);
        Route::post('/notifikasi', [PesananController::class, 'webhook']);
    });
    Route::group(['prefix' => 'akun'], function () {
        Route::get('/', [AkunController::class, 'index']);
        Route::put('/', [AkunController::class, 'editProfile']);
        Route::patch('/', [AkunController::class, 'editPassword']);
    });
});
