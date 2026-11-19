<?php

namespace App\Http\Controllers;

use App\Services\SuperPasswordService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SuperPasswordController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected SuperPasswordService $superPasswordService
    ) {}

    /**
     * Display the Super Password game page.
     */
    public function index(): View
    {
        $initialRound = $this->superPasswordService->generateRound(10);
        $wordBank = $this->superPasswordService->getWordBank();

        return view('games.super-password', [
            'initialRound' => $initialRound,
            'wordBank' => $wordBank,
        ]);
    }

    /**
     * Generate a new set of letters and secret words for a game round.
     */
    public function getRound(Request $request): JsonResponse
    {
        $excludeWords = $request->input('exclude_words', []);
        $items = $this->superPasswordService->generateRound(10, is_array($excludeWords) ? $excludeWords : []);

        return response()->json([
            'status' => 'success',
            'items' => $items,
        ]);
    }
}
