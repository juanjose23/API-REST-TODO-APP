<?php

use Illuminate\Support\Facades\Route;
use Src\modules\auth\oauth\Presentation\Controllers\OAuthController;

Route::prefix('oauth')->group(function () {
    Route::post('/login', [OAuthController::class, 'login']);
    Route::post('/refresh', [OAuthController::class, 'refresh']);
    Route::post('/logout', [OAuthController::class, 'logout']);
    Route::post('/logout-all', [OAuthController::class, 'logoutAll']);

});

Route::middleware(['web'])->group(function () {
    Route::get('/github/redirect', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'github');
    Route::get('/twitter/redirect', [OAuthController::class, 'redirectToProvider'])->defaults('provider', 'twitter');
    Route::get('/github/callback', [OAuthController::class, 'providerCallback'])->defaults('provider', 'github');
    Route::get('/twitter/callback', [OAuthController::class, 'providerCallback'])->defaults('provider', 'twitter');
});
