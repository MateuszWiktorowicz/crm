<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;

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
                'total_price' => number_format($offer->total_price, 2, ',', ' '),
                'created_at' => $offer->created_at->format('d-m-Y'),
                'offer_details' => $offer->offerDetails->map(function ($detail) {
                    return [
                        'toolType' => $detail->toolGeometry->toolType->tool_type_name ?? null, // Dodanie pola toolType
                        'flutesNumber' => $detail->toolGeometry->flutes_number ?? null, // Dodanie liczby rowków
                        'diameter' => $detail->toolGeometry->diameter ?? null, // Dodanie średnicy
                        'tool_geometry_id' => $detail->tool_geometry_id,
                        'tool_quantity' => $detail->tool_quantity,
                        'tool_discount' => $detail->tool_discount,
                        'tool_total_net_price' => $detail->tool_total_net_price,
                        'tool_total_gross_price' => $detail->tool_total_gross_price,
                        'coating_price_id' => $detail->coating_price_id,
                        'coating_quantity' => $detail->coating_quantity,
                        'coating_discount' => $detail->coating_discount,
                        'coating_total_net_price' => $detail->coating_net_price,
                        'coating_total_gross_price' => $detail->coating_gross_price,
                    ];
                }),
            ];
        });

    return response()->json([
        'offers' => $offers,
    ]);
}

    public function store(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|exists:customers,id',
            'status_id' => 'required|exists:statuses,id',
            'total_price' => 'required|numeric',
            'offer_details' => 'required|array',
            'offer_details.*.tool_geometry_id' => 'required|exists:tool_geometries,id',
            'offer_details.*.tool_quantity' => 'required|integer|min:1',
            'offer_details.*.tool_discount' => 'nullable|numeric|min:0|max:100',
            'offer_details.*.tool_total_net_price' => 'required|numeric|min:0',
            'offer_details.*.tool_total_gross_price' => 'required|numeric|min:0',
            'offer_details.*.coating_price_id' => 'nullable|exists:coating_prices,id',
            'offer_details.*.coating_quantity' => 'nullable|integer|min:0',
            'offer_details.*.coating_discount' => 'nullable|numeric|min:0|max:100',
            'offer_details.*.coating_net_price' => 'nullable|numeric|min:0',
            'offer_details.*.coating_gross_price' => 'nullable|numeric|min:0',
        ]);
    
        $validated['created_by'] = $request->user()->id;
    
        DB::beginTransaction();
    
        try {
            $offer = Offer::create([
                'customer_id' => $validated['customer_id'],
                'status_id' => $validated['status_id'],
                'total_price' => $validated['total_price'],
                'created_by' => $validated['created_by'],
                'changed_by' => $validated['created_by'],
            ]);
    
            foreach ($validated['offer_details'] as $detail) {
                if (empty($detail['coating_price_id'])) {
                    $detail['coating_quantity'] = 0;
                    $detail['coating_discount'] = 0;
                    $detail['coating_net_price'] = 0;
                    $detail['coating_gross_price'] = 0;
                }
                $offer->offerDetails()->create($detail);
            }
    
            DB::commit();
    
            return response()->json(['message' => 'Oferta utworzona pomyślnie', 'offer' => $offer], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Wystąpił błąd podczas zapisu oferty', 'message' => $e->getMessage()], 500);
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
}
