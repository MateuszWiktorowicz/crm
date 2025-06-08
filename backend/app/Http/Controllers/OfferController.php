<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use Illuminate\Http\Request;
use App\Models\{Offer, Status, Settings, OfferPdfInfo};
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class OfferController extends Controller
{

    public function show($id)
{
    $offer = Offer::with([
        'customer',
        'offerDetails.coatingPrice.coatingType',
        'offerDetails.toolType',
        'offerDetails.toolGeometry',
        'offerDetails.tool',
        'status',
        'createdBy',
        'changedBy',
        'pdfInfo'
    ])->find($id);

    if (!$offer) {
        return response()->json(['error' => 'Oferta nie istnieje'], 404);
    }

    return response()->json(['offer' => $offer]);
}


    public function index(Request $request)
{
    $offers = Offer::with([
        'customer',
        'offerDetails.coatingPrice.coatingType',
        'offerDetails.toolType',
        'offerDetails.toolGeometry',
        'offerDetails.tool',
        'status',
        'createdBy',
        'changedBy',
        'pdfInfo'
    ])->get();

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
                // Dodaj symbol i file_id do danych przed zapisaniem
                $offer->offerDetails()->create([
                    'offer_id' => $offer->id,
                    'tool_geometry_id' => $detail['tool_geometry_id'],
                        'tool_type_id' => $detail['tool_type_id'] ?? null,  // <-- dodaj toolType id

                    'quantity' => $detail['quantity'],
                    'discount' => $detail['discount'],
                    'tool_net_price' => $detail['tool_net_price'],
                    'coating_price_id' => $detail['coating_price_id'] === 0 ? null : $detail['coating_price_id'], // Jeśli coating_price_id jest 0, ustaw na null
                    'coating_net_price' => $detail['coating_net_price'],
                    'radius' => $detail['radius'],
                    'regrinding_option' => $detail['regrinding_option'],
                    'description' => $detail['description'],
                    'symbol' => $detail['symbol'] ?? null,  // Dodaj symbol
                    'file_id' => $detail['fileId'] ?? null,  // Dodaj file_id
                ]);
            }

            $offer->pdfInfo()->create([
                'delivery_time' => $validated['pdf_info']['deliveryTime'] ?? null,
                'offer_validity' => $validated['pdf_info']['offerValidity'] ?? null,
                'payment_terms' => $validated['pdf_info']['paymentTerms'] ?? null,
                'display_discount' => $validated['pdf_info']['displayDiscount'] ?? false,
            ]);
    
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
            foreach ($validated['offer_details'] as $detail) {
                // Dodaj symbol i file_id do danych przed zapisaniem
                $offer->offerDetails()->create([
                    'offer_id' => $offer->id,
                    'tool_geometry_id' => $detail['tool_geometry_id'],
                        'tool_type_id' => $detail['tool_type_id'] ?? null,  // <-- dodaj toolType id

                    'quantity' => $detail['quantity'],
                'discount' => $detail['discount'] ?? 0,
                    'tool_net_price' => $detail['tool_net_price'],
                    'coating_price_id' => $detail['coating_price_id'] === null ? null : $detail['coating_price_id'], // Jeśli coating_price_id jest 0, ustaw na null
                    'coating_net_price' => $detail['coating_net_price'],
                    'radius' => $detail['radius'],
                    'regrinding_option' => $detail['regrinding_option'],
                    'description' => $detail['description'],
                    'symbol' => $detail['symbol'] ?? null,  // Dodaj symbol
                    'file_id' => $detail['fileId'] ?? null,  // Dodaj file_id
                ]);
            }

            if ($offer->pdfInfo) {
                $offer->pdfInfo()->update([
                    'delivery_time' => $validated['pdf_info']['deliveryTime'] ?? null,
                    'offer_validity' => $validated['pdf_info']['offerValidity'] ?? null,
                    'payment_terms' => $validated['pdf_info']['paymentTerms'] ?? null,
                    'display_discount' => $validated['pdf_info']['displayDiscount'] ?? false,
                ]);
            } else {
                $offer->pdfInfo()->create([
                    'delivery_time' => $validated['pdf_info']['deliveryTime'] ?? null,
                    'offer_validity' => $validated['pdf_info']['offerValidity'] ?? null,
                    'payment_terms' => $validated['pdf_info']['paymentTerms'] ?? null,
                    'display_discount' => $validated['pdf_info']['displayDiscount'] ?? false,
                ]);
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

    public function generateOfferPdf(Request $request, $offerId)
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

        $pdfInfo = $request->only(['deliveryTime', 'offerValidity', 'paymentTerms', 'displayDiscount']);

        OfferPdfInfo::updateOrCreate(
            ['offer_id' => $offerId],
            [
                'delivery_time' => $pdfInfo['deliveryTime'] ?? null,
                'offer_validity' => $pdfInfo['offerValidity'] ?? null,
                'payment_terms' => $pdfInfo['paymentTerms'] ?? null,
                'display_discount' => $pdfInfo['displayDiscount'] ?? false,
            ]
        );
    
if (!$offer->offer_number) {
    $offerNumber = $setting->offer_number + 1;
    
    $setting->update(['offer_number' => $offerNumber]);

    $formattedOfferNumber = $offerNumber . '/' . now()->format('d/m/Y');

    $offer->update(['offer_number' => $formattedOfferNumber]);
    
    // Odśwież model, żeby mieć aktualne dane w $offer
    $offer->refresh();
}

        // Generowanie PDF
        $pdf = Pdf::loadView('offer.pdf', compact('offer', 'offerDetails', 'pdfInfo'))
        ->setPaper('A4', 'portrait')
        ->setOption('isHtml5ParserEnabled', true)
        ->setOption('isPhpEnabled', true)
        ->setOption('encoding', 'UTF-8'); // Ustawienie kodowania UTF-8

    
    
        // Dodaj nagłówki CORS dla odpowiedzi na PDF
        $filename = 'oferta_' . time() . '.pdf'; // Możesz użyć np. $offer->customer->code

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            'Access-Control-Allow-Origin' => '*',
            'Access-Control-Allow-Methods' => 'GET, POST, OPTIONS',
            'Access-Control-Allow-Headers' => 'Origin, Content-Type, X-Requested-With',
        ]);
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
