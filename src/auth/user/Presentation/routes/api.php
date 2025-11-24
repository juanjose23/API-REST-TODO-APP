<?php
use Illuminate\Support\Facades\Route;
use Src\auth\user\presentation\controllers\UserController;

Route::post('/register', [UserController::class, 'register']);

route::get('/', [UserController::class, 'getAllUsers']);
