<?php

use App\Http\Controllers\PelangganController;
use App\Http\Controllers\TransaksiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::post('/signup', [UserController::class, 'signUp']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [UserController::class, 'logout']);
    Route::post('/shift', [UserController::class, 'shift']);
    Route::post('/tambahTransaksi', [TransaksiController::class, 'tambahTransaksi']);
    Route::get('/daftarTransaksi', [TransaksiController::class, 'daftarTransaksi']);
    Route::get('/transaksi/{idtransaksi}', [TransaksiController::class, 'showDetailTransaksi']);
    Route::put('/transaksi/{idtransaksi}/updatestatus', [TransaksiController::class, 'updateStatusTransaksi']);
    Route::put('/detailtransaksi/{id}/updatestatus', [TransaksiController::class, 'updateStatusDetailTransaksi']);
    Route::get('/transaksi/{idtransaksi}/status', [TransaksiController::class, 'getTransaksiStatus']);
});

Route::post('/login', [UserController::class, 'login']);
Route::post('/tambahPelanggan', [PelangganController::class, 'tambahPelanggan']);
