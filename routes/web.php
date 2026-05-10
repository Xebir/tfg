<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BattleController;
use App\Http\Controllers\GameController;
use Illuminate\Support\Facades\Route;

// Pública
Route::get('/', fn() => view('home'))->name('home');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Juego (requiere autenticación)
Route::middleware('auth')->group(function () {
    Route::get('/menu', [GameController::class, 'menu'])->name('menu');
    Route::post('/game/start', [GameController::class, 'start'])->name('game.start');
    Route::post('/game/continue', [GameController::class, 'continue'])->name('game.continue');
    Route::get('/game', [GameController::class, 'show'])->name('game.show');
    Route::post('/game/exit', [GameController::class, 'exit'])->name('game.exit');
    Route::post('/game/finish', [GameController::class, 'finish'])->name('game.finish');

    Route::post('/battle/action', [BattleController::class, 'action'])->name('battle.action');
});
