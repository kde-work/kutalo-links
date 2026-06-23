<?php

use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SpaController;
use Illuminate\Support\Facades\Route;

Route::get('/{slug}', [RedirectController::class, 'redirect'])
    ->where('slug', '[a-zA-Z0-9_-]{2,32}');

Route::fallback([SpaController::class, 'index']);
