<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/negara', [DashboardController::class, 'countries'])
    ->name('countries.index');

Route::get('/risiko', [DashboardController::class, 'risks'])
    ->name('risks.index');

Route::get('/pelabuhan', [DashboardController::class, 'ports'])
    ->name('ports.index');

Route::get('/kurs', [DashboardController::class, 'currencies'])
    ->name('currencies.index');

Route::get('/berita', [DashboardController::class, 'news'])
    ->name('news.index');