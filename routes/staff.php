<?php

use App\Http\Controllers\StaffPortal\DashboardController;
use App\Http\Controllers\StaffPortal\GuestRequestController;
use Illuminate\Support\Facades\Route;

Route::group([
    'prefix' => 'staff',
    'middleware' => ['auth:web', 'staff.portal'],
    'as' => 'staff.',
], function () {
    Route::get('/', DashboardController::class)->name('dashboard');

    Route::get('requests', [GuestRequestController::class, 'index'])->name('requests.index');
    Route::get('requests/{visa_booking}', [GuestRequestController::class, 'show'])->name('requests.show');
    Route::post('requests/{visa_booking}/advance', [GuestRequestController::class, 'advance'])->name('requests.advance');
    Route::post('requests/{visa_booking}/complete', [GuestRequestController::class, 'complete'])->name('requests.complete');
    Route::post('requests/{visa_booking}/note', [GuestRequestController::class, 'updateNote'])->name('requests.update-note');
});
