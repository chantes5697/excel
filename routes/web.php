<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PedimentosController;

Route::get('/', [PedimentosController::class, 'index']);
Route::post('/upload', [PedimentosController::class, 'upload'])->name('upload');
Route::get('/preview', [PedimentosController::class, 'preview'])->name('preview');
Route::post('/store', [PedimentosController::class, 'store'])->name('store');