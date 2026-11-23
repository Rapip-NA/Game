<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\GameSuggestionController;
use App\Http\Controllers\SiapaKembarankuController;
use App\Http\Controllers\SpeakingController;
use App\Http\Controllers\SuperPasswordController;
use App\Http\Controllers\SusunUrutanController;
use App\Models\GameMaintenance;
use Illuminate\Support\Facades\Route;

// Home / Landing Page
Route::get('/', function () {
    try {
        $gameStatuses = GameMaintenance::getStatuses();
    } catch (Throwable) {
        $gameStatuses = [];
    }

    return view('welcome', [
        'gameStatuses' => $gameStatuses,
    ]);
})->name('home');

// Game Suggestion Routes
Route::get('/saran', [GameSuggestionController::class, 'create'])->name('game-suggestions.create');
Route::get('/saran-game', fn () => redirect()->route('game-suggestions.create'));
Route::post('/game-suggestions', [GameSuggestionController::class, 'store'])->name('game-suggestions.store');

// Admin Authentication
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// Admin Panel
Route::prefix('admin')->name('admin.')->middleware('admin.auth')->group(function (): void {
    Route::get('/', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/maintenance/{gameKey}/toggle', [AdminController::class, 'toggleMaintenance'])->name('maintenance.toggle');
    Route::post('/maintenance/{gameKey}/message', [AdminController::class, 'updateMaintenanceMessage'])->name('maintenance.message');
    Route::delete('/suggestions/{suggestion}', [AdminController::class, 'deleteSuggestion'])->name('suggestions.delete');
});

// Games Routes (Protected by Game Maintenance Check)
Route::prefix('games')->name('games.')->group(function (): void {
    // Super Password
    Route::middleware('game.maintenance:super-password')->group(function (): void {
        Route::get('/super-password', [SuperPasswordController::class, 'index'])->name('super-password');
        Route::get('/super-password/round', [SuperPasswordController::class, 'getRound'])->name('super-password.round');
    });

    // Susun Urutan
    Route::middleware('game.maintenance:susun-urutan')->group(function (): void {
        Route::get('/susun-urutan', [SusunUrutanController::class, 'index'])->name('susun-urutan');
        Route::get('/susun-urutan/themes', [SusunUrutanController::class, 'getThemes'])->name('susun-urutan.themes');
        Route::get('/susun-urutan/tim{team}/{player}', [SusunUrutanController::class, 'playerClue'])->name('susun-urutan.player');
    });

    // Siapa Kembaranku?
    Route::prefix('siapa-kembaranku')->name('siapa-kembaranku.')->middleware('game.maintenance:siapa-kembaranku')->group(function (): void {
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

    // Speaking Roulette
    Route::middleware('game.maintenance:speaking')->group(function (): void {
        Route::get('/speaking', [SpeakingController::class, 'index'])->name('speaking');
        Route::get('/speaking/topics', [SpeakingController::class, 'getTopics'])->name('speaking.topics');
    });
});

// Friendly aliases with game maintenance checks
Route::middleware('game.maintenance:super-password')->group(function (): void {
    Route::get('/super-password', [SuperPasswordController::class, 'index']);
});

Route::middleware('game.maintenance:susun-urutan')->group(function (): void {
    Route::get('/susun-urutan', [SusunUrutanController::class, 'index']);
    Route::get('/tim{team}/{player}', [SusunUrutanController::class, 'playerClue']);
});

Route::middleware('game.maintenance:speaking')->group(function (): void {
    Route::get('/speaking', [SpeakingController::class, 'index']);
});

Route::middleware('game.maintenance:siapa-kembaranku')->group(function (): void {
    Route::get('/siapa-kembaranku', fn () => redirect()->route('games.siapa-kembaranku.host'));
    Route::get('/siapa-kembaranku/host', [SiapaKembarankuController::class, 'host']);
    Route::get('/siapa-kembaranku/play', [SiapaKembarankuController::class, 'play']);
    Route::get('/host', [SiapaKembarankuController::class, 'host']);
    Route::get('/play', [SiapaKembarankuController::class, 'play']);
});
