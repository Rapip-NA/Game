<?php

use App\Http\Controllers\SiapaKembarankuController;
use App\Http\Controllers\SuperPasswordController;
use App\Http\Controllers\SusunUrutanController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::prefix('games')->name('games.')->group(function () {
    // Super Password
    Route::get('/super-password', [SuperPasswordController::class, 'index'])->name('super-password');
    Route::get('/super-password/round', [SuperPasswordController::class, 'getRound'])->name('super-password.round');

    // Susun Urutan
    Route::get('/susun-urutan', [SusunUrutanController::class, 'index'])->name('susun-urutan');
    Route::get('/susun-urutan/themes', [SusunUrutanController::class, 'getThemes'])->name('susun-urutan.themes');
    Route::get('/susun-urutan/tim{team}/{player}', [SusunUrutanController::class, 'playerClue'])->name('susun-urutan.player');

    // Siapa Kembaranku?
    Route::prefix('siapa-kembaranku')->name('siapa-kembaranku.')->group(function () {
        Route::get('/host', [SiapaKembarankuController::class, 'host'])->name('host');
        Route::get('/play', [SiapaKembarankuController::class, 'play'])->name('play');
        Route::post('/create', [SiapaKembarankuController::class, 'createGame'])->name('create');
        Route::post('/join', [SiapaKembarankuController::class, 'join'])->name('join');
        Route::post('/start', [SiapaKembarankuController::class, 'start'])->name('start');
        Route::post('/scan', [SiapaKembarankuController::class, 'scan'])->name('scan');
        Route::post('/start-voting', [SiapaKembarankuController::class, 'startVoting'])->name('start-voting');
        Route::post('/vote', [SiapaKembarankuController::class, 'vote'])->name('vote');
        Route::post('/reveal', [SiapaKembarankuController::class, 'reveal'])->name('reveal');
        Route::post('/next-round', [SiapaKembarankuController::class, 'nextRound'])->name('next-round');
        Route::get('/state', [SiapaKembarankuController::class, 'state'])->name('state');
    });
});

// Friendly aliases
Route::get('/super-password', [SuperPasswordController::class, 'index']);
Route::get('/susun-urutan', [SusunUrutanController::class, 'index']);
Route::get('/tim{team}/{player}', [SusunUrutanController::class, 'playerClue']);

// Siapa Kembaranku aliases
Route::get('/siapa-kembaranku', fn () => redirect()->route('games.siapa-kembaranku.host'));
Route::get('/siapa-kembaranku/host', [SiapaKembarankuController::class, 'host']);
Route::get('/siapa-kembaranku/play', [SiapaKembarankuController::class, 'play']);
Route::get('/host', [SiapaKembarankuController::class, 'host']);
Route::get('/play', [SiapaKembarankuController::class, 'play']);
