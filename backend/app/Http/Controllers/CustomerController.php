<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use Illuminate\Http\Response;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
    
        // Sprawdź, czy użytkownik ma odpowiednią rolę
        if ($user->hasRole('admin') || $user->hasRole('regeneration')) {
            // Zwrot wszystkich klientów posortowanych po nazwie
            return Customer::orderBy('name', 'asc')->get();
        } elseif ($user->hasRole('saler')) {
            // Zwrot klientów przypisanych do danego sprzedawcy
            return Customer::where('saler_marker', $user->marker)
                ->orderBy('name', 'asc')
                ->get();
        }

        // Zwrócenie odpowiedzi o braku dostępu, jeśli nie ma wymaganej roli
        return response()->json(['message' => 'Brak dostępu.'], 403);
    }

    public function store(CustomerRequest $request)
    {
        try {
            // Tworzenie nowego klienta z wykorzystaniem walidacji
            $customer = Customer::create($request->validated());
            return response()->json([
                'message' => 'Customer został pomyślnie dodany.',
                'customer' => $customer
            ], 201); // Status 201 oznacza utworzenie zasobu
        } catch (\Exception $e) {
            return response()->json(['message' => 'Wystąpił błąd podczas tworzenia klienta.', 'error' => $e->getMessage()], 500);
        }
    }

    public function destroy(Customer $customer)
    {
        try {
            // Sprawdzanie, czy klient istnieje przed próbą usunięcia
            if (!$customer) {
                return response()->json(['message' => 'Klient nie znaleziony.'], 404);
            }

            $customer->delete(); // Usuwanie klienta
            return response()->json(['message' => 'Klient został pomyślnie usunięty.'], 204);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Wystąpił błąd podczas usuwania klienta.', 'error' => $e->getMessage()], 500);
        }
    }

    public function edit(CustomerRequest $request, Customer $customer)
    {
        try {
            // Sprawdzenie, czy klient istnieje
            if (!$customer) {
                return response()->json(['message' => 'Klient nie znaleziony.'], 404);
            }

            // Aktualizowanie klienta
            $customer->update($request->validated());

            // Zwrócenie informacji o powodzeniu aktualizacji
            return response()->json([
                'message' => 'Customer został pomyślnie zaktualizowany.',
                'customer' => $customer
            ], 200); // Status 200 oznacza, że wszystko przebiegło pomyślnie
        } catch (\Exception $e) {
            return response()->json(['message' => 'Wystąpił błąd podczas aktualizacji klienta.', 'error' => $e->getMessage()], 500);
        }
    }
}
