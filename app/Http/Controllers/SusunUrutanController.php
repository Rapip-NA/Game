<?php

namespace App\Http\Controllers;

use App\Services\SusunUrutanService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SusunUrutanController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected SusunUrutanService $susunUrutanService
    ) {}

    /**
     * Display the Susun Urutan game host page.
     */
    public function index(): View
    {
        $themes = $this->susunUrutanService->getThemes();

        return view('games.susun-urutan', [
            'themes' => $themes,
        ]);
    }

    /**
     * Display the player clue card view when scanned via QR code or direct link.
     */
    public function playerClue(Request $request, int|string $team, int|string $player): View
    {
        $themes = $this->susunUrutanService->getThemes();
        $word = $request->query('w');
        $theme = $request->query('t');

        return view('games.susun-urutan', [
            'themes' => $themes,
            'isPlayerView' => true,
            'teamNum' => (int) $team,
            'playerNum' => (int) $player,
            'word' => $word,
            'themeName' => $theme,
        ]);
    }

    /**
     * Get available themes as JSON.
     */
    public function getThemes(): JsonResponse
    {
        return response()->json([
            'status' => 'success',
            'themes' => $this->susunUrutanService->getThemes(),
        ]);
    }
}
