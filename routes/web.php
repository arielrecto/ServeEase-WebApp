<?php

use App\Models\Page;
use Inertia\Inertia;
use App\Models\ServiceType;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Application;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\GuestController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ServiceTypeController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Admin\BarangayController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Customer\FavoriteController;
use App\Http\Controllers\ServiceProvider\ServiceController;
use App\Http\Controllers\Customer\CustomerFeedbackController;
use App\Http\Controllers\ServiceProvider\PersonalEventController;
use App\Http\Controllers\ServiceProvider\PaymentAccountController;
use App\Http\Controllers\Admin\ServiceProviderApplicationController;
use App\Http\Controllers\ServiceProvider\PaymentTransactionController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\ServiceProviderController as AdminSPController;
use App\Http\Controllers\Admin\ServiceTypeController as ADServiceTypeController;
use App\Http\Controllers\Customer\ServiceController as CustomerServiceController;
use App\Http\Controllers\Customer\ServiceProviderController as CustomerSPController;
use App\Http\Controllers\Customer\DashboardController as CustomerDashboardController;
use App\Http\Controllers\Admin\CustomerFeedbackController as ADCustomerFeedbackController;
use App\Http\Controllers\Customer\ReportController as CustomerReportController;
use App\Http\Controllers\ServiceProvider\BookingController as ServiceProviderBookingController;
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
    // $pages = Page::all();

    return Inertia::render('Index', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
        'services' => App\Models\ServiceType::orderBy('name')->take(6)->get(),
        'totalServices' => App\Models\ServiceType::all()->count(),
        // 'pages' => $pages,
    ]);
});

Route::middleware('guest')->controller(GuestController::class)->as('guest.')->group(function () {
    Route::get('/about', 'about')->name('about');
    Route::get('/search', 'search')->name('search');
    Route::get('/services', 'services')->name('services');
    Route::get('/services/{id}', 'show')->name('show');
    Route::get('/about-us', 'aboutUs')->name('about-us');
    Route::get('/terms-and-condition', 'termsAndCondition')->name('terms-and-condition');
    Route::get('/page/{slug}', 'showPageContent')->name('page.show');
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    Route::get('/home', [HomeController::class, 'index'])->name('home');

    Route::prefix('profile')->as('profile.')->controller(ProfileController::class)->group(function () {
        Route::get('', 'edit')->name('edit');
        Route::patch('', 'update')->name('update');
        Route::delete('', 'destroy')->name('destroy');
        Route::get('/provider', 'provider')->name('provider');
        Route::get('/provider/{providerProfile}', 'showProviderProfile')->name('showProviderProfile');
        Route::post('/update', 'updateProfile')->name('updateProfile');
        Route::put('/update/provider', 'updateProviderProfile')->name('updateProviderProfile');
    })->middleware(['verified']);

    Route::prefix('admin')->as('admin.')->group(function () {
        Route::prefix('cms')->as('cms.')->group(function () {
            Route::get('', [PageController::class, 'index'])->name('index');
            Route::put('/{slug}', [PageController::class, 'update'])->name('update');
            Route::get('/{slug}/edit', [PageController::class, 'edit'])->name('edit');
        });
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
        Route::resource('service-provider', AdminSPController::class);
        Route::prefix('applications')->as('applications.')->controller(ServiceProviderApplicationController::class)->group(function () {
            Route::get('/approve/{id}', 'approve')->name('approve');
            Route::put('/approved/{id}', 'approved')->name('approved');
            Route::delete('/rejected/{id}', 'reject')->name('reject');
            Route::get('/delete/{id}', 'delete')->name('delete');
        });
        Route::resource('applications', ServiceProviderApplicationController::class)->except(["create", "store", "edit", "update"]);
        Route::get('/services/delete/{id}', [AdminServiceController::class, 'delete'])->name('services.delete');
        Route::resource('services', AdminServiceController::class)->except(["create", "store", "index"]);
        Route::resource('service-types', ADServiceTypeController::class)->except('update');
        Route::post('/service-types/{id}', [ADServiceTypeController::class, 'update'])->name('service-types.update');

        Route::get('/feedbacks/delete/{feedback}', [ADCustomerFeedbackController::class, 'delete'])->name('feedbacks.delete');
        Route::resource('feedbacks', ADCustomerFeedbackController::class)->except(['create', 'store']);
        Route::resource('barangays', BarangayController::class)->except(['show']);
        Route::get('/barangays/delete/{id}', [BarangayController::class, 'delete'])->name('barangays.delete');

        Route::prefix('users')->controller(UserController::class)->as('users.')->group(function () {
            Route::get('/{user}/confirm', 'confirm')->name('confirm');
            Route::put('/{user}/restore', 'restore')->name('restore');
        });
        Route::resource('users', UserController::class);

        Route::prefix('reports')->as('reports.')->group(function () {
            Route::put('approve/{report}', [ReportController::class, 'approve'])->name('approve');
            Route::put('reject/{report}', action: [ReportController::class, 'reject'])->name('reject');
            Route::put('resolve/{report}', [ReportController::class, 'resolve'])->name('resolve');
        });

        Route::resource('reports', ReportController::class)->except(['create', 'store']);
    });

    Route::middleware(['verified', 'suspended'])->group(function () {
        Route::middleware(['profile-required'])->prefix('customer')->as('customer.')->group(function () {
            Route::get('dashboard', [CustomerDashboardController::class, 'dashboard'])->name('dashboard');
            Route::prefix('services')->as('services.')->group(function () {
                Route::get('{service}/avail', [CustomerServiceController::class, 'availCreate'])->name('avail.create');
                Route::post('avail', [CustomerServiceController::class, 'availStore'])->name('avail.store');
                Route::get('bulk-forms/{provider_id}', [CustomerServiceController::class, 'bulkForm'])->name('bulk-form');
                Route::post('bulk-forms/store', [CustomerServiceController::class, 'bulkAvail'])->name('bulk-avail');
            });
            Route::prefix('booking')->controller(BookingController::class)->as('booking.')->group(function () {
                Route::get('/cart/{id}', 'showCart')->name('cart.show');
                Route::post('/reply', 'reply')->name('reply');
                Route::get('/{availService}/detail', 'detail')->name('detail');
                Route::get('/payment/{availService}', 'showPayment')->name('payment');
                Route::post('/pay', 'pay')->name('pay');
                Route::get('/{availService}/confirm-cancel', 'confirmCancel')->name('confirm-cancel');
                Route::put('/{availService}/cancel', 'cancel')->name('cancel');
            });
            Route::prefix('feedbacks')->controller(CustomerFeedbackController::class)->as('feedbacks.')->group(function () {
                Route::get('/{feedback}/delete', 'delete')->name('delete');
            });
            Route::resource('feedbacks', CustomerFeedbackController::class);
            Route::prefix('favorites')->as('favorites.')->controller(FavoriteController::class)->group(function () {
                Route::post('/{favorite}', 'add')->name('add');
            });
            Route::resource('favorites', FavoriteController::class)->except(['show', 'create', 'edit', 'update']);
            Route::get('/booking/archive', [BookingController::class, 'showArchive'])->name('booking.archive');
            Route::resource('booking', BookingController::class);
            Route::resource('services', CustomerServiceController::class)->only('show');
            Route::resource('service-provider', CustomerSPController::class)->except(['index', 'show', 'edit', 'update', 'destroy']);
            Route::post('/service-provider/{providerProfile}', [CustomerSPController::class, 'update'])->name('service-provider.update');
            Route::prefix('report')->as('report.')->controller(CustomerReportController::class)->group(function () {
                Route::get('/received', 'receivedComplaints')->name('received');
            });
            Route::resource('report', CustomerReportController::class);
        });

        Route::prefix('service-provider')->as('service-provider.')->group(function () {
            Route::get('dashboard', [ServiceProviderDashboardController::class, 'dashboard'])->name('dashboard');
            Route::get('/services/{service}/archive', [ServiceController::class, 'showArchiveModal'])->name('services.archive');
            Route::post('/services/{service}/archive', [ServiceController::class, 'archive'])->name('services.archive.confirm');
            Route::get('/services/{service}/restore', [ServiceController::class, 'showRestoreModal'])->name('services.restore');
            Route::post('/services/{service}/restore', [ServiceController::class, 'restore'])->name('services.restore.confirm');
            Route::post('/services/{service}/update', [ServiceController::class, 'update'])->name('services.update');
            Route::resource('services', ServiceController::class)->except('update');
            Route::prefix('booking')->as('booking.')->group(function () {
                Route::get('/{serviceCartId}/cart', [ServiceProviderBookingController::class, 'showCart'])->name('cart.show');
                Route::post('cart/{serviceCart}/approve-all', [ServiceProviderBookingController::class, 'approveAll'])
                    ->name('cart.approve-all');
                Route::post('cart/{serviceCart}/reject-all', [ServiceProviderBookingController::class, 'rejectAll'])
                    ->name('cart.reject-all');

                Route::get('', [ServiceProviderBookingController::class, 'index'])->name('index');
                Route::get('/{availService}/detail', [ServiceProviderBookingController::class, 'detail'])->name('detail');
                Route::get('/{availService}/confirm', [ServiceProviderBookingController::class, 'confirm'])->name('confirm');
                Route::put('/{availService}/update/status', [ServiceProviderBookingController::class, 'updateStatus'])->name('update.status');
                Route::post('/reply', [ServiceProviderBookingController::class, 'reply'])->name('reply');
            });
            Route::resource('payment-accounts', PaymentAccountController::class);

            Route::prefix('transactions')->as('transactions.')->controller(PaymentTransactionController::class)->group(function () {
                Route::get('', 'index')->name('index');
                Route::post('/{transaction}/update-status', 'updateStatus')->name('update-status');
            });

            Route::resource('personal-events', PersonalEventController::class)->except(['show']);
        });


        Route::prefix('service-types')->as('types.')->controller(ServiceTypeController::class)->group(function () {
            Route::get('', 'index')->name('index');
            Route::get('/{serviceType}', 'show')->name('show');
        });

        Route::controller(SearchController::class)->prefix('explore')->as('search.')->group(function () {
            Route::get('', 'redirectAuthUser')->name('index')->middleware(['profile-required']);
        });

        Route::get('/messages', [MessageController::class, 'index'])->name('messages.index');
        Route::get('/messages/{conversation}', [MessageController::class, 'show'])->name('messages.show');
        Route::post('/messages/{conversation}', [MessageController::class, 'store'])->name('messages.store');

        Route::put('/notifications/read', [NotificationController::class, 'markAsSeen']);
    });
});

Route::get('/booking/services/{id}', [BookingController::class, 'show'])->name('booking.show');
Route::get('/customer/{service}/feedback', [CustomerServiceController::class, 'getFeedbackByService'])->name('customer.services.feedback');
Route::get('/explore/all', [SearchController::class, 'servicesFilter'])->name('search.filter');

require __DIR__ . '/auth.php';
