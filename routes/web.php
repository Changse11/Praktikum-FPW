<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth'])->name('dashboard');

Route::get('/about', function () {
    return 'Barokah Mart adalah toko kelontong yang menyediakan sembako, minuman, makanan ringan, dan kebutuhan rumah tangga.';
})->name('about');
