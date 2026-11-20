<?php

namespace App\Http\Controllers;

use App\Services\SpeakingService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SpeakingController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        protected SpeakingService $speakingService
    ) {}

    /**
     * Display the Speaking game page.
     */
    public function index(): View
    {
        $topics = $this->speakingService->getRandomTopics(30);
        $totalTopics = $this->speakingService->totalTopics();

        return view('games.speaking', [
            'topics' => $topics,
            'totalTopics' => $totalTopics,
        ]);
    }

    /**
     * Return a fresh batch of random topics as JSON.
     */
    public function getTopics(Request $request): JsonResponse
    {
        $count = min((int) $request->input('count', 30), 100);
        $topics = $this->speakingService->getRandomTopics($count);

        return response()->json([
            'status' => 'success',
            'topics' => $topics,
            'total_available' => $this->speakingService->totalTopics(),
        ]);
    }
}
