<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/photo/{path}', function ($path) {
    abort_unless(Storage::disk('local')->exists($path), 404);
    return Storage::disk('local')->response($path);
})->where('path', '.*')->name('photo.show');