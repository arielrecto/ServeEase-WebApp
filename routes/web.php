<?php

use App\Http\Controllers\SearchController;
use Inertia\Inertia;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\ServiceProvider\ServiceController;
use App\Http\Controllers\Admin\ServiceProviderController as AdminSPController;
use App\Http\Controllers\Customer\ServiceProviderController as CustomerSPController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\ServiceProvider\DashboardController as ServiceProviderDashboardController;
use App\Http\Controllers\GuestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return Inertia::render('Index', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'services' => App\Models\ServiceType::orderBy('name')->take(6)->get(),
        'totalServices' => App\Models\ServiceType::all()->count(),
    ]);
});

Route::middleware('guest')->controller(GuestController::class)->as('guest.')->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/search', 'search')->name('search');
    Route::get('/services', 'services')->name('services');
    Route::get('/services/{id}', 'show')->name('show');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {


    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.updateProfile');
    Route::put('/profile/update/provider', [ProfileController::class, 'updateProviderProfile'])->name('profile.updateProviderProfile');


    Route::prefix('admin')->as('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::resource('service-provider', AdminSPController::class);
        Route::prefix('service-provider')->as('service-provider.')->group(function () {
            Route::get('/approve/{id}', [AdminSPController::class, 'approved'])->name('approve');
            Route::put('/approved/{id}', [AdminSPController::class, 'approved'])->name('approved');
            Route::get('/delete/{id}', [AdminSPController::class, 'delete'])->name('delete');
        });
        Route::resource('service-types', ServiceTypeController::class)->except('update');
        Route::post('/service-types/{id}', [ServiceTypeController::class, 'update'])->name('service-types.update');

        Route::resource('barangays', BarangayController::class)->except(['show']);
        Route::get('/barangays/delete/{id}', [BarangayController::class, 'delete'])->name('barangays.delete');
    });

    Route::middleware(['profile-required'])->prefix('customer')->as('customer.')->group(function () {
        Route::get('dashboard', [CustomerDashboardController::class, 'dashboard'])->name('dashboard');

        Route::resource('service-provider', CustomerSPController::class)->except(['index', 'show', 'edit', 'update', 'destroy']);
    });

    Route::prefix('service-provider')->as('service-provider.')->group(function () {
        Route::get('dashboard', [ServiceProviderDashboardController::class, 'dashboard'])->name('dashboard');
        Route::resource('services', ServiceController::class);
    });

    Route::controller(SearchController::class)->prefix('explore')->as('search.')->group(function () {
        Route::get('', 'redirectAuthUser')->name('index')->middleware(['profile-required']);
    });
});

require __DIR__ . '/auth.php';
