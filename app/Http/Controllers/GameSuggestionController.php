<?php

namespace App\Http\Controllers;

use App\Models\GameSuggestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class GameSuggestionController extends Controller
{
    /**
     * Store a new game suggestion from a user.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'game_title' => ['required', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:1500'],
        ], [
            'name.required' => 'Nama atau panggilan kamu wajib diisi ya!',
            'game_title.required' => 'Judul atau ide game wajib diisi!',
            'description.required' => 'Ceritain sedikit konsep game yang kamu mau ya!',
            'description.max' => 'Konsep game maksimal 1500 karakter.',
        ]);

        $suggestion = GameSuggestion::create([
            'name' => trim($validated['name']),
            'game_title' => trim($validated['game_title']),
            'description' => trim($validated['description']),
            'status' => 'pending',
        ]);

        $message = 'Makasih banyak! Saran ide game kamu sudah diterima admin MainYuk 🎉';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $suggestion,
            ]);
        }

        return back()->with('suggestion_success', $message);
    }
}
