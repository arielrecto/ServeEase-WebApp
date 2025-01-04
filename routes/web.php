<?php

use Inertia\Inertia;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ServiceTypeController;
use App\Http\Controllers\ServiceProvider\ServiceController;
use App\Http\Controllers\Customer\CustomerFeedbackController;
use App\Http\Controllers\Admin\ServiceProviderApplicationController;
use App\Http\Controllers\Admin\ServiceProviderController as AdminSPController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Customer\ServiceProviderController as CustomerSPController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Admin\CustomerFeedbackController as ADCustomerFeedbackController;
use App\Http\Controllers\ServiceProvider\DashboardController as ServiceProviderDashboardController;


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

    Route::prefix('profile')->as('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('', 'edit')->name('edit');
        Route::patch('', 'update')->name('update');
        Route::delete('', 'destroy')->name('destroy');
        Route::get('/provider', 'provider')->name('provider');
        Route::get('/provider/{providerProfile}', 'showProviderProfile')->name('showProviderProfile');
        Route::post('/update', 'updateProfile')->name('updateProfile');
        Route::put('/update/provider', 'updateProviderProfile')->name('updateProviderProfile');
    });

    Route::prefix('admin')->as('admin.')->group(function () {
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::resource('service-provider', AdminSPController::class);
        Route::prefix('service-provider')->as('service-provider.')->group(function () {
            Route::get('/approve/{id}', [AdminSPController::class, 'approve'])->name('approve');
            Route::put('/approved/{id}', [AdminSPController::class, 'approved'])->name('approved');
            Route::get('/delete/{id}', [AdminSPController::class, 'delete'])->name('delete');
        });
        // Route::prefix('applications')->as('applications.')->controller(ServiceProviderApplicationController::class)->group(function () {
        //     Route::get('/approve/{id}', 'approve')->name('approve');
        //     Route::put('/approved/{id}', 'approved')->name('approved');
        //     Route::get('/delete/{id}', 'delete')->name('delete');
        // });
        Route::resource('applications', ServiceProviderApplicationController::class)->except(["create", "store", "edit", "update"]);
        Route::resource('service-types', ServiceTypeController::class)->except('update');
        Route::post('/service-types/{id}', [ServiceTypeController::class, 'update'])->name('service-types.update');

        Route::get('/feedbacks/delete/{feedback}', [ADCustomerFeedbackController::class, 'delete'])->name('feedbacks.delete');
        Route::resource('feedbacks', ADCustomerFeedbackController::class)->except(['create', 'store']);
        Route::resource('barangays', BarangayController::class)->except(['show']);
        Route::get('/barangays/delete/{id}', [BarangayController::class, 'delete'])->name('barangays.delete');

        Route::prefix('users')->controller(UserController::class)->as('users.')->group(function () {
            Route::get('/{user}/confirm', 'confirm')->name('confirm');
            Route::put('/{user}/restore', 'restore')->name('restore');
        });
        Route::resource('users', UserController::class);
    });

    Route::middleware(['profile-required'])->prefix('customer')->as('customer.')->group(function () {
        Route::get('dashboard', [CustomerDashboardController::class, 'dashboard'])->name('dashboard');
        Route::prefix('services')->as('services.')->group(function () {
            Route::get('{service}/avail', [CustomerServiceController::class, 'availCreate'])->name('avail.create');
            Route::post('avail', [CustomerServiceController::class, 'availStore'])->name('avail.store');
        });
        Route::prefix('booking')->controller(BookingController::class)->as('booking.')->group(function () {
            Route::get('/{availService}/detail', 'detail')->name('detail');
        });
        Route::prefix('feedbacks')->controller(CustomerFeedbackController::class)->as('feedbacks.')->group(function () {
            Route::get('/{feedback}/delete', 'delete')->name('delete');
        });
        Route::resource('feedbacks', CustomerFeedbackController::class);
        Route::resource('booking', BookingController::class);
        Route::resource('services', CustomerServiceController::class)->only('show');
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

Route::get('/booking/services/{id}', [BookingController::class, 'show'])->name('booking.show');
Route::get('/customer/{service}/feedback', [CustomerServiceController::class, 'getFeedbackByService'])->name('customer.services.feedback');
Route::get('/explore/all', [SearchController::class, 'servicesFilter'])->name('search.filter');

require __DIR__ . '/auth.php';
