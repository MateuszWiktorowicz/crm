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
        $user = $request->user();  

        // Buduj query z eager loading
        $query = Offer::with([
            'customer',
            'offerDetails.coatingPrice.coatingType',
            'offerDetails.toolType',
            'offerDetails.toolGeometry',
            'offerDetails.tool',
            'status',
            'createdBy',
            'changedBy',
            'pdfInfo'
        ]);

        // Filtrowanie po uprawnieniach użytkownika
        if (!$user->hasRole('admin') && !$user->hasRole('regeneration')) {
            $query->where('created_by', $user->id);
        }

        // Filtrowanie po numerze oferty
        if ($request->has('offer_number') && $request->offer_number) {
            $query->where('offer_number', 'LIKE', '%' . $request->offer_number . '%');
        }

        // Filtrowanie po nazwie klienta
        if ($request->has('customer_name') && $request->customer_name) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->customer_name . '%');
            });
        }

        // Filtrowanie po nazwie pracownika
        if ($request->has('employee_name') && $request->employee_name) {
            $query->whereHas('createdBy', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->employee_name . '%');
            });
        }

        // Filtrowanie po statusie
        if ($request->has('status_name') && $request->status_name) {
            $query->whereHas('status', function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->status_name . '%');
            });
        }

        // Filtrowanie po dacie utworzenia
        if ($request->has('created_at') && $request->created_at) {
            $query->whereDate('created_at', 'LIKE', '%' . $request->created_at . '%');
        }

        // Sortowanie i paginacja
        $perPage = $request->input('per_page', 10);
        $offers = $query->orderBy('created_at', 'desc')->paginate($perPage);

        $statuses = Status::all();

        return response()->json([
            'data' => $offers->items(),
            'meta' => [
                'current_page' => $offers->currentPage(),
                'per_page' => $offers->perPage(),
                'total' => $offers->total(),
                'last_page' => $offers->lastPage(),
            ],
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

            $offer->load([
    'pdfInfo',
    'offerDetails.toolGeometry.toolType',
    'offerDetails.coatingPrice.coatingType',
    'status',
    'customer',
    'createdBy',
    'changedBy'
]);
    
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

            $offer->load([
    'pdfInfo',
    'offerDetails.toolGeometry.toolType',
    'offerDetails.coatingPrice.coatingType',
    'status',
    'customer',
    'createdBy',
    'changedBy'
]);
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
    $offerNumber = $setting->offer_number;
    
    $setting->update(['offer_number' => $offerNumber + 1]);

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

    public function dashboard(Request $request)
    {
        $user = $request->user();
        
        // Pobierz statusy
        $statusRobocza = Status::where('name', 'Robocza')->first();
        $statusWyslana = Status::where('name', 'Wysłana')->first();
        $statusZamowienie = Status::where('name', 'Zamówienie')->first();
        $statusOdrzucona = Status::where('name', 'Odrzucona')->first();
        
        // Buduj query z filtrowaniem
        $query = Offer::with(['status', 'customer', 'createdBy']);
        
        // Filtrowanie po uprawnieniach użytkownika
        if (!$user->hasRole('admin') && !$user->hasRole('regeneration')) {
            $query->where('created_by', $user->id);
        }
        
        // Filtrowanie po kliencie
        if ($request->has('customer_id') && $request->customer_id) {
            $query->where('customer_id', $request->customer_id);
        }
        
        // Filtrowanie po handlowcu (po znaczniku z klienta)
        if ($request->has('employee_marker') && $request->employee_marker) {
            $query->whereHas('customer', function ($q) use ($request) {
                $q->where('saler_marker', $request->employee_marker);
            });
        }
        
        // Filtrowanie po okresie
        $period = $request->input('period', 'month'); // week, month, year, custom
        $startDate = null;
        $endDate = null;
        
        if ($period === 'week') {
            $startDate = now()->startOfWeek();
            $endDate = now()->endOfWeek();
        } elseif ($period === 'month') {
            $startDate = now()->startOfMonth();
            $endDate = now()->endOfMonth();
        } elseif ($period === 'year') {
            $startDate = now()->startOfYear();
            $endDate = now()->endOfYear();
        } elseif ($period === 'custom' && $request->has('start_date') && $request->has('end_date')) {
            $startDate = \Illuminate\Support\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Illuminate\Support\Carbon::parse($request->end_date)->endOfDay();
        }
        
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }
        
        $offers = $query->get();
        
        // Oblicz statystyki
        $stats = [
            'inPreparation' => $offers->where('status_id', $statusRobocza?->id)->count(),
            'sent' => $offers->where('status_id', $statusWyslana?->id)->count(),
            'accepted' => $offers->where('status_id', $statusZamowienie?->id)->count(),
            'rejected' => $offers->where('status_id', $statusOdrzucona?->id)->count(),
        ];
        
        // Wartość wygranych vs. przegranych
        $wonValue = $offers->where('status_id', $statusZamowienie?->id)->sum('total_price');
        // Przegrane to tylko oferty odrzucone (nie wysłane!)
        $lostValue = $offers->where('status_id', $statusOdrzucona?->id)->sum('total_price');
        
        // Wartość ofert w okresie
        $totalValue = $offers->sum('total_price');
        
        // Wartość w zależności od wybranego okresu - to jest wartość dla wybranego okresu
        $periodValue = $totalValue;
        
        // Funkcja pomocnicza do zastosowania filtrów do query
        $applyFilters = function ($baseQuery) use ($user, $request) {
            // Filtrowanie po uprawnieniach użytkownika
            if (!$user->hasRole('admin') && !$user->hasRole('regeneration')) {
                $baseQuery->where('created_by', $user->id);
            }
            
            // Filtrowanie po kliencie
            if ($request->has('customer_id') && $request->customer_id) {
                $baseQuery->where('customer_id', $request->customer_id);
            }
            
            // Filtrowanie po handlowcu (po znaczniku z klienta)
            if ($request->has('employee_marker') && $request->employee_marker) {
                $baseQuery->whereHas('customer', function ($q) use ($request) {
                    $q->where('saler_marker', $request->employee_marker);
                });
            }
            
            return $baseQuery;
        };
        
        // Wartość miesięczna, kwartalna, roczna (z zastosowanymi filtrami)
        $monthlyQuery = $applyFilters(Offer::query())
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
        
        $quarterlyQuery = $applyFilters(Offer::query())
            ->whereBetween('created_at', [
                now()->startOfQuarter(),
                now()->endOfQuarter()
            ]);
        
        $yearlyQuery = $applyFilters(Offer::query())
            ->whereYear('created_at', now()->year);
        
        $monthlyValue = $monthlyQuery->sum('total_price');
        $quarterlyValue = $quarterlyQuery->sum('total_price');
        $yearlyValue = $yearlyQuery->sum('total_price');
        
        // Skuteczność ofert (%) - tylko zaakceptowane + odrzucone (bez wysłanych)
        $totalDecided = $offers->whereIn('status_id', [
            $statusZamowienie?->id,
            $statusOdrzucona?->id
        ])->count();
        
        $successRate = $totalDecided > 0 
            ? ($stats['accepted'] / $totalDecided) * 100 
            : 0;
        
        return response()->json([
            'stats' => $stats,
            'wonValue' => round($wonValue, 2),
            'lostValue' => round($lostValue, 2),
            'totalValue' => round($totalValue, 2),
            'periodValue' => round($periodValue, 2),
            'monthlyValue' => round($monthlyValue, 2),
            'quarterlyValue' => round($quarterlyValue, 2),
            'yearlyValue' => round($yearlyValue, 2),
            'successRate' => round($successRate, 2),
            'period' => $period,
            'startDate' => $startDate?->toDateString(),
            'endDate' => $endDate?->toDateString(),
        ]);
    }
}
