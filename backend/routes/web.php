<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDriverController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\DriverBookingController;
use App\Http\Controllers\DriverDashboardController;
use App\Http\Controllers\DriverServiceController;
use App\Http\Controllers\DriverTripController;
use App\Http\Controllers\ParentChildController;
use App\Http\Controllers\ParentDashboardController;
use App\Http\Controllers\ParentDriverController;
use App\Http\Controllers\ParentRatingController;
use App\Http\Controllers\ParentTripController;
use App\Http\Controllers\LandingController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;

Route::get('/', [LandingController::class, 'show'])->name('landing');

// Temporary route to run migrations on Render free tier
Route::get('/force-migrate-db', function () {
    try {
        Artisan::call('migrate', ['--force' => true]);
        return 'Migration run successfully: <br><pre>' . Artisan::output() . '</pre>';
    } catch (\Exception $e) {
        return 'Migration failed: ' . $e->getMessage();
    }
});

Route::get('/clear-cache', function () {
    try {
        Artisan::call('route:clear');
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('view:clear');
        return 'Cache cleared successfully';
    } catch (\Exception $e) {
        return 'Cache clear failed: ' . $e->getMessage();
    }
});

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

Route::get('/forgot-password', [PasswordResetLinkController::class, 'create'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset.otp');

Route::get('/reset-password/{token}', [NewPasswordController::class, 'create'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

Route::get('/register/parent', [RegisteredUserController::class, 'createParent'])
    ->name('register.parent')
    ->middleware('guest');

Route::post('/register/parent', [RegisteredUserController::class, 'storeParent'])
    ->middleware('guest');

Route::get('/register/driver', [RegisteredUserController::class, 'createDriver'])
    ->name('register.driver')
    ->middleware('guest');

Route::post('/register/driver', [RegisteredUserController::class, 'storeDriver'])
    ->middleware('guest');

Route::get('/parent/dashboard', ParentDashboardController::class)
    ->name('parent.dashboard')
    ->middleware(['auth', 'role:parent']);

Route::middleware(['auth', 'role:parent'])->group(function () {
    Route::get('/parent/children/create', [ParentChildController::class, 'create'])
        ->name('parent.children.create');

    Route::post('/parent/children', [ParentChildController::class, 'store'])
        ->name('parent.children.store');

    Route::get('/parent/children/{child}/edit', [ParentChildController::class, 'edit'])
        ->name('parent.children.edit');

    Route::put('/parent/children/{child}', [ParentChildController::class, 'update'])
        ->name('parent.children.update');

    Route::get('/parent/children/{child}/trips', [ParentTripController::class, 'index'])
        ->name('parent.children.trips.index');

    Route::get('/parent/children/{child}/drivers', [ParentDriverController::class, 'show'])
        ->name('parent.children.drivers.show');

    Route::post('/parent/children/{child}/drivers', [ParentDriverController::class, 'store'])
        ->name('parent.children.drivers.store');

    Route::get('/parent/trips/{trip}', [ParentTripController::class, 'show'])
        ->name('parent.trips.show');

    Route::get('/parent/trips/{trip}/location', [ParentTripController::class, 'location'])
        ->name('parent.trips.location');

    Route::get('/parent/trips/{trip}/rate', [ParentRatingController::class, 'create'])
        ->name('parent.trips.rate.create');

    Route::post('/parent/trips/{trip}/rate', [ParentRatingController::class, 'store'])
        ->name('parent.trips.rate.store');
    
    Route::get('/parent/live-trips', [ParentTripController::class, 'live'])
        ->name('parent.trips.live');
});

Route::get('/driver/dashboard', DriverDashboardController::class)
    ->name('driver.dashboard')
    ->middleware(['auth', 'role:driver']);

Route::middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/driver/service-area', [DriverServiceController::class, 'create'])
        ->name('driver.service.create');

    Route::post('/driver/service-area', [DriverServiceController::class, 'store'])
        ->name('driver.service.store');

    Route::get('/driver/bookings', [DriverBookingController::class, 'index'])
        ->name('driver.bookings.index');

    Route::post('/driver/bookings/{booking}/approve', [DriverBookingController::class, 'approve'])
        ->name('driver.bookings.approve');

    Route::post('/driver/bookings/{booking}/decline', [DriverBookingController::class, 'decline'])
        ->name('driver.bookings.decline');

    Route::get('/driver/trips', [DriverTripController::class, 'index'])
        ->name('driver.trips.index');

    Route::post('/driver/trips/start', [DriverTripController::class, 'startRun'])
        ->name('driver.trips.start');

    Route::post('/driver/trips/{trip}/start', [DriverTripController::class, 'start'])
        ->name('driver.trips.start-single');

    Route::post('/driver/trips/{trip}/events', [DriverTripController::class, 'logEvent'])
        ->name('driver.trips.events.store');

    Route::post('/driver/location', [DriverTripController::class, 'updateDriverLocation'])
        ->name('driver.location.update');

    Route::get('/driver/trips/history', [DriverTripController::class, 'history'])
        ->name('driver.trips.history');
    
    Route::get('/driver/ratings', [App\Http\Controllers\DriverRatingController::class, 'index'])
        ->name('driver.ratings.index');
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
        
    Route::get('/admin/users', [AdminUserController::class, 'index'])
        ->name('admin.users.index');

    Route::post('/admin/users/{user}/suspend', [AdminUserController::class, 'suspend'])
        ->name('admin.users.suspend');

    Route::get('/admin/drivers/pending', [AdminDriverController::class, 'indexPending'])
        ->name('admin.drivers.pending');

    Route::post('/admin/drivers/{driver}/approve', [AdminDriverController::class, 'approve'])
        ->name('admin.drivers.approve');

    Route::post('/admin/drivers/{driver}/reject', [AdminDriverController::class, 'reject'])
        ->name('admin.drivers.reject');

    Route::get('/admin/reports', [AdminReportController::class, 'index'])
        ->name('admin.reports.index');
});
