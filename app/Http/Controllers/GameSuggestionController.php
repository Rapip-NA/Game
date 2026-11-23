<?php

namespace App\Http\Controllers;

use App\Models\GameSuggestion;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\View\View;

class GameSuggestionController extends Controller
{
    /**
     * Show the form to submit a game suggestion.
     */
    public function create(): View
    {
        return view('suggestions.create');
    }

    /**
     * Store a new game suggestion from a user.
     */
    public function store(Request $request): JsonResponse|RedirectResponse
    {
        $validated = $request->validate([
            'game_title' => ['nullable', 'string', 'max:150'],
            'description' => ['required', 'string', 'max:10000'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:5120'],
        ], [
            'description.required' => 'Deskripsi atau konsep gamenya wajib diisi ya!',
            'description.max' => 'Konsep game maksimal 10.000 karakter.',
            'image.image' => 'File lampiran harus berupa gambar.',
            'image.mimes' => 'Format gambar yang didukung: JPG, PNG, GIF, WEBP.',
            'image.max' => 'Ukuran gambar maksimal 5MB.',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imageFile = $request->file('image');
            $uploadDir = public_path('uploads/suggestions');

            if (! File::isDirectory($uploadDir)) {
                File::makeDirectory($uploadDir, 0755, true);
            }

            $extension = $imageFile->getClientOriginalExtension() ?: 'png';
            $filename = time().'_'.Str::random(12).'.'.$extension;
            $imageFile->move($uploadDir, $filename);

            $imagePath = 'uploads/suggestions/'.$filename;
        }

        // Sanitize rich-text HTML description allowing only safe formatting tags
        $cleanDescription = strip_tags(
            $validated['description'],
            '<p><br><b><strong><i><em><u><s><ul><ol><li><h1><h2><h3><h4><blockquote><code><pre>'
        );

        $gameTitle = trim($validated['game_title'] ?? '');
        if ($gameTitle === '') {
            $gameTitle = 'Ide Game Tanpa Judul';
        }

        $suggestion = GameSuggestion::create([
            'name' => 'Anonim',
            'game_title' => $gameTitle,
            'description' => $cleanDescription,
            'image_path' => $imagePath,
            'status' => 'pending',
        ]);

        $message = 'Makasih banyak! Saran ide game kamu sudah berhasil dikirim dan akan dibaca admin MainYuk 🎉';

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'status' => 'success',
                'message' => $message,
                'data' => $suggestion,
            ]);
        }

        return redirect()->route('game-suggestions.create')->with('suggestion_success', $message);
    }
}
