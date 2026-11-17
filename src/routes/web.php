<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;
use Livewire\Livewire;

/* NOTE: Do Not Remove
/ Livewire asset handling if using sub folder in domain
*/

Livewire::setUpdateRoute(function ($handle) {
    return Route::post(config('app.asset_prefix') . '/livewire/update', $handle);
});

Livewire::setScriptRoute(function ($handle) {
    return Route::get(config('app.asset_prefix') . '/livewire/livewire.js', $handle);
});
/*
/ END
*/
Route::get('/', HomeController::class)->name('home');

Route::get('/events', [EventController::class, 'index'])->name('events.index');
Route::get('/events/{slug}', [EventController::class, 'show'])->name('events.show');
Route::get('/events/{slug}/register', [RegistrationController::class, 'create'])->name('events.register');
Route::post('/events/{slug}/register', [RegistrationController::class, 'store'])
    ->middleware('throttle:20,1')
    ->name('events.register.store');

Route::get('/tickets/{ticketCode}', [TicketController::class, 'show'])->name('tickets.show');
