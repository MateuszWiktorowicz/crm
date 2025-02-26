<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
    
        if ($user->hasRole('admin') || $user->hasRole('regeneration')) {
            return Customer::orderBy('name', 'asc')->get(); // Poprawne sortowanie
        } else if ($user->hasRole('saler')) {
            return Customer::where('saler_marker', $user->marker)
                ->orderBy('name', 'asc') // Sortowanie rosnące
                ->get();
        }
    }
    
    

    public function store(CustomerRequest $request)
    {
        return Customer::create($request->validated());
    }

    public function destroy(Customer $customer)
    {
        $customer->delete();

        return response(null, 204);
    }

    public function edit(CustomerRequest $request, Customer $customer)
    {
        $customer->update($request->validated());

        return response()->json([
            'message' => 'Customer został pomyślnie zaktualizowany.',
            'customer' => $customer
        ], 200);
    }
}
