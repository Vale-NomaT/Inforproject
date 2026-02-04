<?php

use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminDriverController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
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

Route::get('/', [LandingController::class, 'show'])->name('landing');

Route::get('/login', [AuthenticatedSessionController::class, 'create'])
    ->name('login')
    ->middleware('guest');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->name('logout')
    ->middleware('auth');

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

    Route::get('/parent/trips/{trip}/rate', [ParentRatingController::class, 'create'])
        ->name('parent.trips.rate.create');

    Route::post('/parent/trips/{trip}/rate', [ParentRatingController::class, 'store'])
        ->name('parent.trips.rate.store');
});

Route::get('/driver/dashboard', DriverDashboardController::class)
    ->name('driver.dashboard')
    ->middleware(['auth', 'role:driver']);

Route::middleware(['auth', 'role:driver'])->group(function () {
    Route::get('/driver/bookings', [DriverBookingController::class, 'index'])
        ->name('driver.bookings.index');

    Route::post('/driver/bookings/{booking}/approve', [DriverBookingController::class, 'approve'])
        ->name('driver.bookings.approve');

    Route::post('/driver/bookings/{booking}/decline', [DriverBookingController::class, 'decline'])
        ->name('driver.bookings.decline');

    Route::get('/driver/trips', [DriverTripController::class, 'index'])
        ->name('driver.trips.index');

    Route::get('/driver/map', [DriverTripController::class, 'map'])
        ->name('driver.map');

    Route::get('/driver/trips/history', [DriverTripController::class, 'history'])
        ->name('driver.trips.history');

    Route::get('/driver/service-area', [DriverServiceController::class, 'edit'])
        ->name('driver.service.edit');

    Route::put('/driver/service-area', [DriverServiceController::class, 'update'])
        ->name('driver.service.update');

    Route::post('/driver/trips/{trip}/start', [DriverTripController::class, 'start'])
        ->name('driver.trips.start');

    Route::post('/driver/trips/{trip}/location', [DriverTripController::class, 'updateLocation'])
        ->name('driver.trips.location');

    Route::post('/driver/trips/{trip}/events', [DriverTripController::class, 'logEvent'])
        ->name('driver.trips.events');
});

Route::get('/admin/dashboard', AdminDashboardController::class)
    ->name('admin.dashboard')
    ->middleware(['auth', 'role:admin']);

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'role:admin'])
    ->group(function () {
        Route::get('/drivers/pending', [AdminDriverController::class, 'indexPending'])
            ->name('drivers.pending');

        Route::post('/drivers/{driver}/approve', [AdminDriverController::class, 'approve'])
            ->name('drivers.approve');

        Route::post('/drivers/{driver}/reject', [AdminDriverController::class, 'reject'])
            ->name('drivers.reject');

        Route::get('/users', [AdminUserController::class, 'index'])
            ->name('users.index');

        Route::post('/users/{user}/suspend', [AdminUserController::class, 'suspend'])
            ->name('users.suspend');

        Route::get('/reports/trips', [AdminReportController::class, 'trips'])
            ->name('reports.trips');

        Route::get('/reports/signups', [AdminReportController::class, 'signups'])
            ->name('reports.signups');

        Route::get('/reports/driver-performance', [AdminReportController::class, 'driverPerformance'])
            ->name('reports.driver-performance');
    });
