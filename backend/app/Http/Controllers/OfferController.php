<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use Illuminate\Http\Request;
use App\Models\{Offer, Status, Settings};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class OfferController extends Controller
{
    public function index(Request $request)
{
    $offers = Offer::with(['offerDetails.toolGeometry', 'offerDetails.coatingPrice', 'createdBy', 'customer', 'status'])
        ->get()
        ->map(function ($offer) {
            return [
                'id' => $offer->id,
                'customer_name' => $offer->customer->name,
                'customer_id' => $offer->customer->id,
                'employee_name' => $offer->createdBy->name,
                'status_name' => $offer->status->name,
                'total_net_price' => number_format($offer->total_price, 2, ',', ' '),
                'created_at' => $offer->created_at->format('d-m-Y'),
                'offer_number' => $offer->offer_number,
                'offer_details' => $offer->offerDetails->map(function ($detail) {
                    return [
                        'toolType' => $detail->toolGeometry->toolType->tool_type_name ?? null, 
                        'flutesNumber' => $detail->toolGeometry->flutes_number ?? null,
                        'diameter' => $detail->toolGeometry->diameter ?? null,
                        'tool_geometry_id' => $detail->tool_geometry_id,
                        'quantity' => $detail->quantity,
                        'discount' => $detail->discount,
                        'tool_net_price' => $detail->tool_net_price,
                        'coating_price_id' => $detail->coating_price_id,
                        'coatingCode' => $detail->coatingPrice->coatingType->mastermet_code ?? null,
                        'coating_net_price' => $detail->coating_net_price,
                        'radius' => $detail->radius,
                        'regrinding_option' => $detail->regrinding_option,
                        'description' => $detail->description
                    ];
                }),
            ];
        });

        $statuses = Status::all();

    return response()->json([
        'offers' => $offers,
        'statuses' => $statuses
    ]);
}

    public function store(OfferRequest $request)
    {
        $validated = $request->validated();
        $validated['created_by'] = $request->user()->id;
    
        DB::beginTransaction();
    
        try {
            $offer = Offer::create([
                'customer_id' => $validated['customer_id'],
                'status_id' => $validated['status_id'],
                'total_price' => $validated['total_net_price'],
                'created_by' => $validated['created_by'],
                'changed_by' => $validated['created_by'],
            ]);
    
            foreach ($validated['offer_details'] as $detail) {
                // if (empty($detail['coating_price_id'])) {
                //     $detail['coating_net_price'] = 0;
                // }
                $offer->offerDetails()->create($detail);
            }
    
            DB::commit();
    
            return response()->json(['message' => 'Oferta utworzona pomyślnie', 'offer' => $offer], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Wystąpił błąd podczas zapisu oferty', 'message' => $e->getMessage()], 500);
        }
    }  

    public function edit(OfferRequest $request, Offer $offer)
    {
        $validated = $request->validated();

        $validated['changed_by'] = $request->user()->id;

        DB::beginTransaction();

        try {
            $offer->update([
                'customer_id' => $validated['customer_id'],
                'status_id' => $validated['status_id'],
                'total_price' => $validated['total_net_price'],
                'changed_by' => $validated['changed_by'],
            ]);

            $offer->offerDetails()->delete();
            foreach ($validated['offer_details'] as &$detail) {
                if (!isset($detail['coating_price_id'])) {
                    // $detail = array_merge($detail, [
                    //     'coating_net_price' => 0,
                    // ]);
                }
                $offer->offerDetails()->create($detail);
            }
            DB::commit();

            return response()->json(['message' => 'Oferta zaktualizowana pomyślnie', 'offer' => $offer], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Wystąpił błąd podczas aktualizacji oferty', 'message' => $e->getMessage()], 500);
        }
    }


    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $offer = Offer::where('id', $id)->whereHas('status', function ($query) {
                $query->where('name', 'Robocza');
            })->first();

            if (!$offer) {
                return response()->json(['error' => 'Oferta nie istnieje lub nie ma statusu "robocza"'], 404);
            }

            $offer->offerDetails()->delete();
            $offer->delete();

            DB::commit();

            return response()->json(['message' => 'Oferta i jej szczegóły zostały usunięte'], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Wystąpił błąd podczas usuwania oferty', 'message' => $e->getMessage()], 500);
        }
    }

    public function generateOfferPdf($offerId)
    {
        $setting = Settings::first();
        $offer = Offer::findOrFail($offerId);  // Załaduj ofertę na podstawie ID
        
        // Sprawdzenie, czy oferta istnieje i ma szczegóły
        if (!$offer) {
            // Możesz zwrócić jakąś informację o błędzie lub wyjątek
            return response()->json(['error' => 'Oferta nie istnieje'], 404);
        }
    
        $offerDetails = $offer->offerDetails; // Szczegóły oferty
        
        // Sprawdzenie, czy szczegóły oferty są dostępne
        if (!$offerDetails || $offerDetails->isEmpty()) {
            // Możesz zwrócić jakąś informację o braku szczegółów oferty
            return response()->json(['error' => 'Brak szczegółów oferty'], 404);
        }
    
        // Generowanie PDF
        $pdf = Pdf::loadView('offer.pdf', compact('offer', 'offerDetails'))
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isPhpEnabled', true)
        ->setOption('encoding', 'UTF-8'); // Ustawienie kodowania UTF-8

        if (!$offer->offer_number) {
            // Jeśli numer oferty nie jest przypisany, zaktualizuj numer oferty
            $offerNumber = $setting->offer_number + 1;
    
            // Zaktualizuj numer oferty w tabeli settings
            $setting->update([
                'offer_number' => $offerNumber
            ]);
    
            // Formatowanie numeru oferty jako numer/dzień/miesiąc/rok
            $formattedOfferNumber = $offerNumber . '/' . now()->format('d/m/Y');
            
            // Zaktualizuj numer oferty w tabeli offers
            $offer->update([
                'offer_number' => $formattedOfferNumber
            ]);
        }
    
        // Dodaj nagłówki CORS dla odpowiedzi na PDF
        return $pdf->download('offer_' . $offerId . '.pdf')
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
                    ->header('Access-Control-Allow-Headers', 'Origin, Content-Type, X-Requested-With');
    }

    public function updateOfferNumber(Request $request, $id)
{
    $validated = $request->validate([
        'offer_number' => 'required|string|max:255',  // Walidacja numeru oferty
    ]);

    $offer = Offer::findOrFail($id);
    $offer->offer_number = $validated['offer_number'];
    $offer->save();

    return response()->json([
        'message' => 'Offer number updated successfully',
        'offer' => $offer,
    ]);
}
}
