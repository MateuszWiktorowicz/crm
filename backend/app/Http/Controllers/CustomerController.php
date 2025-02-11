<?php

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    public function index()
    {
        return Customer::all();
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
