<?php

use App\Http\Controllers\AdminPortController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PortRiskController;
use App\Http\Controllers\PortSyncController;
use App\Http\Controllers\WorldBankSyncController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Guest Routes
|--------------------------------------------------------------------------
| Route ini hanya dapat diakses ketika pengguna belum login.
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])
        ->name('login');

    Route::post('/login', [AuthController::class, 'login'])
        ->name('login.process');

    Route::get('/register', [AuthController::class, 'showRegisterForm'])
        ->name('register');

    Route::post('/register', [AuthController::class, 'register'])
        ->name('register.process');
});

/*
|--------------------------------------------------------------------------
| Logout
|--------------------------------------------------------------------------
*/

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
*/

Route::prefix('api')->group(function () {
    Route::get('/countries', [ApiController::class, 'countries']);
    Route::get('/risk', [ApiController::class, 'risk']);
    Route::get('/ports', [ApiController::class, 'ports']);
    Route::get('/news', [ApiController::class, 'news']);
    Route::get('/currency', [ApiController::class, 'currency']);
    Route::get('/summary', [ApiController::class, 'summary']);
});

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
| Seluruh halaman utama hanya dapat diakses setelah pengguna login.
*/

Route::middleware('auth')->group(function () {
    /*
    |--------------------------------------------------------------------------
    | Dashboard
    |--------------------------------------------------------------------------
    */

    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | Negara
    |--------------------------------------------------------------------------
    */

    Route::get('/negara', [DashboardController::class, 'countries'])
        ->name('countries.index');

    /*
    |--------------------------------------------------------------------------
    | Risiko
    |--------------------------------------------------------------------------
    */

    Route::get('/risiko', [DashboardController::class, 'risks'])
        ->name('risks.index');

    /*
    |--------------------------------------------------------------------------
    | Pelabuhan
    |--------------------------------------------------------------------------
    */

    Route::get('/pelabuhan', [DashboardController::class, 'ports'])
        ->name('ports.index');

    /*
    |--------------------------------------------------------------------------
    | Kurs
    |--------------------------------------------------------------------------
    */

    Route::get('/kurs', [DashboardController::class, 'currencies'])
        ->name('currencies.index');

    /*
    |--------------------------------------------------------------------------
    | Berita
    |--------------------------------------------------------------------------
    */

    Route::get('/berita', [DashboardController::class, 'news'])
        ->name('news.index');

    /*
    |--------------------------------------------------------------------------
    | Perbandingan
    |--------------------------------------------------------------------------
    */

    Route::get('/perbandingan', [DashboardController::class, 'comparisons'])
        ->name('comparisons.index');

    /*
    |--------------------------------------------------------------------------
    | Watchlist
    |--------------------------------------------------------------------------
    */

    Route::get('/watchlist', [DashboardController::class, 'watchlists'])
        ->name('watchlists.index');

    Route::post('/watchlist', [DashboardController::class, 'storeWatchlist'])
        ->name('watchlists.store');

    Route::delete('/watchlist/{id}', [DashboardController::class, 'destroyWatchlist'])
        ->whereNumber('id')
        ->name('watchlists.destroy');

    /*
    |--------------------------------------------------------------------------
    | Laporan
    |--------------------------------------------------------------------------
    */

    Route::get('/laporan', [DashboardController::class, 'reports'])
        ->name('reports.index');

    /*
    |--------------------------------------------------------------------------
    | Admin Routes
    |--------------------------------------------------------------------------
    | Route ini hanya dapat diakses oleh pengguna dengan role admin.
    */

    Route::middleware('admin')->group(function () {
        /*
        |--------------------------------------------------------------------------
        | Panel Admin
        |--------------------------------------------------------------------------
        */

        Route::get('/admin', [DashboardController::class, 'admin'])
            ->name('admin.index');

        /*
        |--------------------------------------------------------------------------
        | Sinkronisasi API Umum
        |--------------------------------------------------------------------------
        */

        Route::post('/admin/api/sync', [DashboardController::class, 'syncExternalApis'])
            ->name('admin.api.sync');

        Route::post('/admin/world-bank/sync', [WorldBankSyncController::class, 'sync'])
            ->name('admin.world_bank.sync');

        /*
        |--------------------------------------------------------------------------
        | Sinkronisasi dan Risk Scoring Negara
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/admin/countries/sync-api',
            [DashboardController::class, 'syncCountriesFromApi']
        )->name('admin.countries.sync_api');

        Route::post(
            '/admin/risk/recalculate',
            [DashboardController::class, 'recalculateRiskScores']
        )->name('admin.risk.recalculate');

        /*
        |--------------------------------------------------------------------------
        | Sinkronisasi dan Risk Scoring Pelabuhan
        |--------------------------------------------------------------------------
        | Route statis diletakkan sebelum route pelabuhan dengan parameter ID.
        */

        Route::post(
            '/admin/ports/sync-world-port-index',
            [PortSyncController::class, 'sync']
        )->name('admin.ports.sync_world_port_index');

        Route::post(
            '/admin/ports/recalculate-risk',
            [PortRiskController::class, 'recalculate']
        )->name('admin.ports.recalculate_risk');

        /*
        |--------------------------------------------------------------------------
        | CRUD Negara
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/admin/countries',
            [DashboardController::class, 'storeCountry']
        )->name('admin.countries.store');

        Route::get(
            '/admin/countries/{id}/edit',
            [DashboardController::class, 'editCountry']
        )
            ->whereNumber('id')
            ->name('admin.countries.edit');

        Route::put(
            '/admin/countries/{id}',
            [DashboardController::class, 'updateCountry']
        )
            ->whereNumber('id')
            ->name('admin.countries.update');

        Route::delete(
            '/admin/countries/{id}',
            [DashboardController::class, 'destroyCountry']
        )
            ->whereNumber('id')
            ->name('admin.countries.destroy');

        /*
        |--------------------------------------------------------------------------
        | CRUD Pelabuhan
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/admin/ports',
            [AdminPortController::class, 'store']
        )->name('admin.ports.store');

        Route::get(
            '/admin/ports/{id}/edit',
            [AdminPortController::class, 'edit']
        )
            ->whereNumber('id')
            ->name('admin.ports.edit');

        Route::put(
            '/admin/ports/{id}',
            [AdminPortController::class, 'update']
        )
            ->whereNumber('id')
            ->name('admin.ports.update');

        Route::delete(
            '/admin/ports/{id}',
            [AdminPortController::class, 'destroy']
        )
            ->whereNumber('id')
            ->name('admin.ports.destroy');

        /*
        |--------------------------------------------------------------------------
        | CRUD Artikel
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/admin/articles',
            [DashboardController::class, 'storeArticle']
        )->name('admin.articles.store');

        Route::get(
            '/admin/articles/{id}/edit',
            [DashboardController::class, 'editArticle']
        )
            ->whereNumber('id')
            ->name('admin.articles.edit');

        Route::put(
            '/admin/articles/{id}',
            [DashboardController::class, 'updateArticle']
        )
            ->whereNumber('id')
            ->name('admin.articles.update');

        Route::delete(
            '/admin/articles/{id}',
            [DashboardController::class, 'destroyArticle']
        )
            ->whereNumber('id')
            ->name('admin.articles.destroy');

        /*
        |--------------------------------------------------------------------------
        | CRUD User
        |--------------------------------------------------------------------------
        */

        Route::post(
            '/admin/users',
            [DashboardController::class, 'storeUser']
        )->name('admin.users.store');

        Route::get(
            '/admin/users/{id}/edit',
            [DashboardController::class, 'editUser']
        )
            ->whereNumber('id')
            ->name('admin.users.edit');

        Route::put(
            '/admin/users/{id}',
            [DashboardController::class, 'updateUser']
        )
            ->whereNumber('id')
            ->name('admin.users.update');

        Route::delete(
            '/admin/users/{id}',
            [DashboardController::class, 'destroyUser']
        )
            ->whereNumber('id')
            ->name('admin.users.destroy');
    });
});