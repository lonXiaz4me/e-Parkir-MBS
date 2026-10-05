<?php

namespace App\Http\Controllers;

class SettingsController extends Controller
{
    /**
     * Display the Tetapan (settings) page.
     * All settings are stored client-side in localStorage — no DB reads needed.
     */
    public function index()
    {
        return view('auth.settings');
    }
}