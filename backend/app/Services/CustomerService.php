<?php
namespace App\Services;

use App\Models\Customer;
use App\Services\ResponseService;
use App\Http\Requests\CustomerRequest;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerService
{
    use AuthorizesRequests;

    protected $responseService;

    public function __construct(ResponseService $responseService)
    {
        $this->responseService = $responseService;
    }

    public function index(Request $request)
    {
        $user = $request->user();

        // Buduj query
        $query = Customer::query();

        // Filtrowanie po uprawnieniach użytkownika
        if ($user->hasRole('admin') || $user->hasRole('regeneration')) {
            // Admin i regeneration widzą wszystkich klientów
        } elseif ($user->hasRole('saler')) {
            $query->where('saler_marker', $user->marker);
        } else {
            return response()->json(['message' => 'Brak dostępu.'], 403);
        }

        // Filtrowanie przez query params
        if ($request->has('code') && $request->code) {
            $query->where('code', 'LIKE', '%' . $request->code . '%');
        }

        if ($request->has('name') && $request->name) {
            $query->where('name', 'LIKE', '%' . $request->name . '%');
        }

        if ($request->has('nip') && $request->nip) {
            $query->where('nip', 'LIKE', '%' . $request->nip . '%');
        }

        if ($request->has('city') && $request->city) {
            $query->where('city', 'LIKE', '%' . $request->city . '%');
        }

        if ($request->has('address') && $request->address) {
            $query->where('address', 'LIKE', '%' . $request->address . '%');
        }

        if ($request->has('saler_marker') && $request->saler_marker) {
            $query->where('saler_marker', 'LIKE', '%' . $request->saler_marker . '%');
        }

        if ($request->has('description') && $request->description) {
            $query->where('description', 'LIKE', '%' . $request->description . '%');
        }

        // Sortowanie i paginacja
        $perPage = $request->input('per_page', 10);
        $customers = $query->orderBy('name', 'asc')->paginate($perPage);

        return response()->json([
            'data' => $customers->items(),
            'meta' => [
                'current_page' => $customers->currentPage(),
                'per_page' => $customers->perPage(),
                'total' => $customers->total(),
                'last_page' => $customers->lastPage(),
            ],
        ]);
    }

    public function store(CustomerRequest $request)
    {
        try {
             $data = $request->validated();

                        $mappedData = [
    'code' => $data['code'] ?? null,
    'name' => $data['name'] ?? null,
    'nip' => $data['nip'] ?? null,
    'zip_code' => $data['zipCode'] ?? null,
    'city' => $data['city'] ?? null,
    'address' => $data['address'] ?? null,
    'saler_marker' => $data['salerMarker'] ?? null,
    'description' => $data['description'] ?? null,
];
            $this->authorize('create', [Customer::class, $mappedData]);


    
            $customer = Customer::create($mappedData);
    
            return response()->json([
                'message' => 'Klient został pomyślnie dodany.',
                'customer' => $customer
            ], 201);
    
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            return response()->json([
                'errors' => [ 'customer' => 'Brak uprawnień do utworzenia klienta.' ]
            ], 403);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Wystąpił błąd podczas tworzenia klienta.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function edit(CustomerRequest $request, Customer $customer)
    {
        try {
                         $data = $request->validated();

                        $mappedData = [
    'code' => $data['code'] ?? null,
    'name' => $data['name'] ?? null,
    'nip' => $data['nip'] ?? null,
    'zip_code' => $data['zipCode'] ?? null,
    'city' => $data['city'] ?? null,
    'address' => $data['address'] ?? null,
    'saler_marker' => $data['salerMarker'] ?? null,
    'description' => $data['description'] ?? null,
];
            $this->authorize('update', $customer);

            if (!$customer) {
                return response()->json([
                    'errors' => [
                        'customer' => ['Klient nie znaleziony.']
                    ]
                ], 404);
            }


            $customer->update($mappedData);

        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Obsługa wyjątku, jeśli użytkownik nie ma uprawnień
            return response()->json([
                'errors' => [
                    'customer' => ['Brak uprawnień do edytowania tego klienta.']
                ]
            ], 403);
        } catch (\Exception $e) {
            // Obsługa ogólnych błędów
            return response()->json([
                'errors' => [
                    'server' => ['Wystąpił błąd podczas edytowania klienta.', $e->getMessage()]
                ]
            ], 500);
        }
    }

    /**
     * Delete customer
     *
     * @param  Customer $customer
     * @return array
     */
     public function destroy(Customer $customer)
     {
        try {
            // Sprawdzanie uprawnień użytkownika do usunięcia klienta
            $this->authorize('delete', $customer);
    
            // Jeśli klient nie istnieje
            if (!$customer) {
                return response()->json([
                    'errors' => [
                        'customer' => ['Klient nie znaleziony.']
                    ]
                ], 404);
            }
    
            // Sprawdzanie, czy klient ma powiązane oferty
            if ($customer->offers()->exists()) {
                return response()->json([
                    'errors' => [
                        'customer' => [
                            "Klient {$customer->name} nie może zostać usunięty, ponieważ ma powiązane oferty."
                        ]
                    ]
                ], 400); // Tu zamykamy nawias
            }
    
            // Usuwanie klienta
            $customer->delete();
            return response()->json(['message' => 'Klient został pomyślnie usunięty.'], 204);
    
        } catch (\Illuminate\Auth\Access\AuthorizationException $e) {
            // Obsługa wyjątku, jeśli użytkownik nie ma uprawnień
            return response()->json([
                'errors' => [
                    'customer' => ['Brak uprawnień do usunięcia tego klienta.']
                ]
            ], 403);
        } catch (\Exception $e) {
            // Obsługa ogólnych błędów
            return response()->json([
                'errors' => [
                    'server' => ['Wystąpił błąd podczas usuwania klienta.', $e->getMessage()]
                ]
            ], 500);
        }
    }
        
}
