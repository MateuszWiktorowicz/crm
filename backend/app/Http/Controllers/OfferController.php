<?php

namespace App\Http\Controllers;

use App\Http\Requests\OfferRequest;
use Illuminate\Http\Request;
use App\Models\Offer;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Response;

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
                'offer_details' => $offer->offerDetails->map(function ($detail) {
                    return [
                        'toolType' => $detail->toolGeometry->toolType->tool_type_name ?? null, 
                        'flutesNumber' => $detail->toolGeometry->flutes_number ?? null,
                        'diameter' => $detail->toolGeometry->diameter ?? null,
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
                    $detail = array_merge($detail, [
                        'coating_quantity' => 0,
                        'coating_discount' => 0,
                        'coating_net_price' => 0,
                        'coating_gross_price' => 0,
                    ]);
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
}
