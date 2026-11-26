<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class SpeakUpController extends Controller
{
    /**
     * Display the SPEAK UP! interactive speaking game page.
     */
    public function index(): View
    {
        return view('games.speak-up');
    }
}
