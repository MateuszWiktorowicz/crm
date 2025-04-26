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

        if ($user->hasRole('admin') || $user->hasRole('regeneration')) {

            return Customer::orderBy('name', 'asc')->get();
        } elseif ($user->hasRole('saler')) {

            return Customer::where('saler_marker', $user->marker)
                ->orderBy('name', 'asc')
                ->get();
        }

        return response()->json(['message' => 'Brak dostępu.'], 403);
    }

    public function store(CustomerRequest $request)
    {
        try {

            $this->authorize('create', [Customer::class, $request->validated()]);
    
            $customer = Customer::create($request->validated());
    
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
            $this->authorize('update', $customer);

            if (!$customer) {
                return response()->json([
                    'errors' => [
                        'customer' => ['Klient nie znaleziony.']
                    ]
                ], 404);
            }


            $customer->update($request->validated());

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
