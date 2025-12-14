<?php

use App\Http\Controllers\TeamsController;
use App\Http\Controllers\TwitterController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Middleware\EnsureJwtNotRevoked;


Route::prefix('v2/auth')->group(function () {
    require base_path('src/modules/auth/user/Presentation/routes/api.php');
    require base_path('src/modules/auth/oauth/Presentation/routes/api.php');
});

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout-all', [AuthController::class, 'logoutAll']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::post('/forgot-password', [AuthController::class, 'sendPasswordResetLink']);
    Route::post('/password-reset', [AuthController::class, 'resetPassword'])->name('password.reset');

});
Route::get('/users', [AuthController::class, 'users'])->name('users');

Route::middleware(['jwt', EnsureJwtNotRevoked::class])->prefix('teams')->group(function () {
    Route::get('/teams', [TeamsController::class, 'teams'])->name('teams.teams');
    Route::get('/team/{team}', [TeamsController::class, 'getTeamById']);
    Route::post('/create', [TeamsController::class, 'createTeam'])->name('teams.create');
    Route::get('/getTeamById/{team}', [TeamsController::class, 'getTeamById']);
    //ROUTES FOR TEAMS
    Route::get('/teams/{teamId}/available-users', [AuthController::class, 'availableUsers'])->name('available-users');
    // ROUTES FOR TEAM INVITATION
    Route::get('/getInvitationByToken/{token}', [TeamsController::class, 'getInvitationByToken'])->name('teams.getInvitationByToken');
    Route::get('listInvitation/{id}', [TeamsController::class, 'listInvitation'])->name('teams.listInvitation');
    Route::get('listInvitationteam/{id}', [TeamsController::class, 'listInvitationteam'])->name('teams.listInvitation');

    Route::post('/addMemberToTeam', [TeamsController::class, 'addMemberToTeam'])->name('teams.addMemberToTeam');
    Route::post('/invitationResponse', [TeamsController::class, 'invitationResponse'])->name('teams.invitationResponse');
});

Route::middleware(['web'])->group(function () {
    Route::get('/auth/twitter', [TwitterController::class, 'redirectToTwitter']);
    Route::get('/auth/twitter/callback', [TwitterController::class, 'handleTwitterCallback']);
});
