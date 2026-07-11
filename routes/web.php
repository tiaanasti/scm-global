<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ApiController;

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

Route::get('/perbandingan', [DashboardController::class, 'comparisons'])
    ->name('comparisons.index');

Route::get('/watchlist', [DashboardController::class, 'watchlists'])
    ->name('watchlists.index');
Route::post('/watchlist', [DashboardController::class, 'storeWatchlist'])
    ->name('watchlists.store');
Route::delete('/watchlist/{id}', [DashboardController::class, 'destroyWatchlist'])
    ->name('watchlists.destroy');

Route::get('/admin', [DashboardController::class, 'admin'])
    ->name('admin.index');
Route::post('/admin/countries', [DashboardController::class, 'storeCountry'])
    ->name('admin.countries.store');

Route::prefix('api')->group(function () {
    Route::get('/countries', [ApiController::class, 'countries']);
    Route::get('/risk', [ApiController::class, 'risk']);
    Route::get('/ports', [ApiController::class, 'ports']);
    Route::get('/news', [ApiController::class, 'news']);
    Route::get('/currency', [ApiController::class, 'currency']);
    Route::get('/summary', [ApiController::class, 'summary']);
});

Route::get('/laporan', [DashboardController::class, 'reports'])
    ->name('reports.index');