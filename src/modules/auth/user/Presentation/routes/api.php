<?php

use Illuminate\Support\Facades\Route;
use Src\modules\auth\user\Presentation\controllers\UserController;
use Src\modules\auth\user\Presentation\controllers\VerifyEmailController;

Route::post('/register', [UserController::class, 'register']);

Route::get('/', [UserController::class, 'getAllUsers']);
Route::get('/verify-email/{token}', [VerifyEmailController::class, 'verify']);
Route::get('/resend-email', [VerifyEmailController::class, 'resend']);
