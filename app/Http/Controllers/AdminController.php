<?php

namespace App\Http\Controllers;

use App\Models\GameMaintenance;
use App\Models\GameSuggestion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminController extends Controller
{
    /**
     * Display the admin panel dashboard.
     */
    public function dashboard(): View
    {
        $gameStatuses = GameMaintenance::getStatuses();
        $suggestions = GameSuggestion::latest()->get();

        $stats = [
            'total_games' => count(GameMaintenance::AVAILABLE_GAMES),
            'in_maintenance' => collect($gameStatuses)->where('is_maintenance', true)->count(),
            'active_games' => collect($gameStatuses)->where('is_maintenance', false)->count(),
            'total_suggestions' => $suggestions->count(),
        ];

        return view('admin.dashboard', [
            'gameStatuses' => $gameStatuses,
            'suggestions' => $suggestions,
            'stats' => $stats,
        ]);
    }

    /**
     * Toggle maintenance mode for a specific game.
     */
    public function toggleMaintenance(Request $request, string $gameKey): RedirectResponse
    {
        if (! array_key_exists($gameKey, GameMaintenance::AVAILABLE_GAMES)) {
            return back()->with('error', 'Game tidak dikenali.');
        }

        $game = GameMaintenance::firstOrCreate(
            ['game_key' => $gameKey],
            [
                'game_name' => GameMaintenance::AVAILABLE_GAMES[$gameKey],
                'is_maintenance' => false,
                'message' => 'Maaf ya gamenya lagi di maintanance sama admin, jadi gak bisa dipake dlu.',
            ]
        );

        $game->is_maintenance = ! $game->is_maintenance;

        if ($request->filled('message')) {
            $game->message = (string) $request->input('message');
        }

        $game->save();

        $statusLabel = $game->is_maintenance ? 'DIKUNCI (SEDANG MAINTENANCE)' : 'DIAKTIFKAN KEMBALI (BISA DIMAINKAN)';

        return back()->with('success', "Status game {$game->game_name} berhasil diubah menjadi: {$statusLabel}!");
    }

    /**
     * Update maintenance message for a game.
     */
    public function updateMaintenanceMessage(Request $request, string $gameKey): RedirectResponse
    {
        if (! array_key_exists($gameKey, GameMaintenance::AVAILABLE_GAMES)) {
            return back()->with('error', 'Game tidak dikenali.');
        }

        $request->validate([
            'message' => ['required', 'string', 'max:255'],
        ]);

        $game = GameMaintenance::firstOrCreate(
            ['game_key' => $gameKey],
            ['game_name' => GameMaintenance::AVAILABLE_GAMES[$gameKey]]
        );

        $game->message = (string) $request->input('message');
        $game->save();

        return back()->with('success', "Pesan maintenance untuk {$game->game_name} berhasil diperbarui!");
    }

    /**
     * Delete a game suggestion.
     */
    public function deleteSuggestion(GameSuggestion $suggestion): RedirectResponse
    {
        $suggestion->delete();

        return back()->with('success', 'Saran game berhasil dihapus.');
    }
}
