<?php
namespace App\Http\Controllers;

use App\Models\Settings;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    // Pobieranie pojedynczego ustawienia
    public function index()
    {
        // Zwracamy jedno ustawienie, jeżeli istnieje
        $setting = Settings::first();

        // Jeżeli ustawienie nie istnieje, zwróć pustą odpowiedź
        return response()->json($setting);
    }

    // Zapisanie nowego ustawienia
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'offerNumber' => 'required|integer|min:1',
        ]);

        $setting = Settings::create([
            'offer_number' => $validatedData['offerNumber'],
        ]);

        return response()->json($setting, 201);
    }

    // Aktualizacja ustawienia
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'offerNumber' => 'required|integer|min:1',
        ]);

        $setting = Settings::findOrFail($id);
        $setting->update([
            'offer_number' => $validatedData['offerNumber'],
        ]);

        return response()->json($setting);
    }
}
