<?php

use App\Domain\Link\ReservedSlugs;
use App\Http\Controllers\RedirectController;
use App\Http\Controllers\SpaController;
use Illuminate\Support\Facades\Route;

foreach (ReservedSlugs::all() as $reservedSlug) {
    Route::get('/'.$reservedSlug, [SpaController::class, 'index']);
}

Route::get('/{slug}', [RedirectController::class, 'redirect'])
    ->where('slug', '[a-zA-Z0-9_-]{1,32}');

Route::fallback([SpaController::class, 'index']);
