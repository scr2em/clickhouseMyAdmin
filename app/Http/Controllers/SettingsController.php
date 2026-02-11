<?php

namespace App\Http\Controllers;

use App\Services\SettingsService;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index(SettingsService $settings)
    {
        $apiKey = $settings->get('anthropic_api_key');
        $maskedKey = $apiKey ? str_repeat('*', max(0, strlen($apiKey) - 4)) . substr($apiKey, -4) : '';

        return view('settings.index', [
            'hasApiKey' => !empty($apiKey),
            'maskedKey' => $maskedKey,
        ]);
    }

    public function store(Request $request, SettingsService $settings)
    {
        $request->validate([
            'anthropic_api_key' => 'required|string|min:10',
        ]);

        $settings->set('anthropic_api_key', $request->input('anthropic_api_key'));

        return redirect()->route('settings.index')->with('success', 'Settings saved successfully.');
    }
}
