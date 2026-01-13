<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/people', function () {
    return view('people');
})->middleware(['auth', 'verified'])->name('people');

Route::get('/settings', function () {
    return view('settings');
})->middleware(['auth', 'verified'])->name('settings');

Route::get('/export', \App\Livewire\ExportManager::class)->middleware(['auth', 'verified'])->name('export');

Route::get('/immich/asset/{id}/{type?}', [\App\Http\Controllers\ImmichProxyController::class, 'show'])
    ->middleware(['auth', 'verified'])
    ->name('immich.asset');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Language Switcher
Route::get('lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'nl'])) {
        session()->put('locale', $locale);
    }
    return redirect()->back();
})->name('lang.switch');

require __DIR__ . '/auth.php';
