<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Modules\Restaurant\Filament\Pages\SelfOrder;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return redirect('/dashboard/login');
})->name('login');

Route::get('/photo/{path}', function ($path) {
    abort_unless(Storage::disk('local')->exists($path), 404);
    return Storage::disk('local')->response($path);
})->where('path', '.*')->name('photo.show');

// Route::middleware(['web', 'throttle:60,1'])
//     ->get('/self-order', SelfOrder::class)
//     ->name('self-order');