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
}
