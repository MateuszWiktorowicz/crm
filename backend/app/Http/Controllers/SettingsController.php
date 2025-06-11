<?php
namespace App\Http\Controllers;
use App\Models\Offer;
use Carbon\Carbon;

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

public function store(Request $request)
{
    $validatedData = $request->validate([
        'offerNumber' => 'required|integer|min:1',
    ]);

    $year = Carbon::now()->year;

    // Pobierz maksymalny numer ofertowy z danego roku
    $maxOfferNumber = Offer::whereYear('created_at', $year)
        ->selectRaw('MAX(CAST(SUBSTRING_INDEX(offer_number, "/", 1) AS UNSIGNED)) as max_number')
        ->value('max_number');

    // Jeśli brak ofert, traktuj max jako 0
    $maxOfferNumber = $maxOfferNumber ?? 0;

    if ($validatedData['offerNumber'] <= $maxOfferNumber) {
        return response()->json([
            'message' => "Numer musi być większy niż ostatnio użyty numer ({$maxOfferNumber}) w roku {$year}.",
        ], 422);
    }

    $setting = Settings::create([
        'offer_number' => $validatedData['offerNumber'],
    ]);

    return response()->json($setting, 201);
}

public function update(Request $request, $id)
{
    $validatedData = $request->validate([
        'offerNumber' => 'required|integer|min:0',
    ]);

    $year = Carbon::now()->year;

    $maxOfferNumber = Offer::whereYear('created_at', $year)
        ->selectRaw('MAX(CAST(SUBSTRING_INDEX(offer_number, "/", 1) AS UNSIGNED)) as max_number')
        ->value('max_number');

    $maxOfferNumber = $maxOfferNumber ?? -1;

    if ($validatedData['offerNumber'] <= $maxOfferNumber) {
        return response()->json([
            'message' => "Numer musi być większy niż ostatnio użyty numer ({$maxOfferNumber}) w roku {$year}.",
        ], 422);
    }

    $setting = Settings::findOrFail($id);
    $setting->update([
        'offer_number' => $validatedData['offerNumber'],
    ]);

    return response()->json($setting);
}
}
