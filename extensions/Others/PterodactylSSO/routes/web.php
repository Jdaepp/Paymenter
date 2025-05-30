<?php

use Illuminate\Support\Facades\Route;
use Paymenter\Extensions\Others\PterodactylSSO\Http\Controllers\PterodactylSSOController;

Route::middleware([
    'web',
    'auth',
    'throttle:10,1'
])->group(function () {
    Route::get('/pterodactyl/sso', [PterodactylSSOController::class, 'redirect'])
        ->name('pterodactyl.sso');
});
