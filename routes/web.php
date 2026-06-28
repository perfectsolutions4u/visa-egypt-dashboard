<?php

use App\Events\NewBookingEvent;
use App\Http\Controllers\Dashboard\DashboardHomeController;
use App\Http\Controllers\Api\HotelController;
use App\Http\Controllers\Dashboard\HotelRoomBookingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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
require __DIR__.'/auth.php';
require __DIR__.'/admin.php';
require __DIR__.'/staff.php';

Route::redirect('/', '/login');

Route::get('/dashboard', DashboardHomeController::class)->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('dashboard/hotels/{hotel}/rooms', [HotelRoomBookingController::class, 'getRoomsByHotel'])
    ->name('dashboard.hotels.rooms');

Route::get('/test',function () {
//    $booking= \App\Models\Booking::find(53);
//    event(new NewBookingEvent($booking));
//    $start = now();
//    $blog = \App\Models\Blog::find(7);
//    $data = \App\Services\Translation\Translator::translate($blog->description, 'fr');
//    $blog->update([
//        'fr' => [
//            'description' => $data
//        ]
//    ]);
//    dd($data, now()->diffForHumans($start));
//    $c = \App\Models\CustomTrip::find(5);
//    event(new \App\Events\NewCustomTripRequestEvent($c));

//    $booking = \App\Models\Booking::latest()->first();
//    dd($booking->meta);
//    event(new \App\Events\NewBookingEvent($booking));
//    dd('SENT!');
//    $social_links = \App\Models\Setting::whereOptionKey('social_links')->first()?->option_value;
//    return view('emails.client.new-booking', compact('booking','social_links'));
});
