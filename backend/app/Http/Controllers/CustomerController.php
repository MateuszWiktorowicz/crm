<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CustomerRequest;
use App\Models\Customer;
use App\Services\CustomerService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class CustomerController extends Controller
{
    protected $customerService;

    public function __construct(CustomerService $customerService)
    {
        $this->customerService = $customerService;
    }

    use AuthorizesRequests;
    public function index(Request $request)
    {
        return $this->customerService->index($request);
    }

    public function store(CustomerRequest $request)
    {
        return $this->customerService->store($request);
    }

    public function destroy(Customer $customer)
    {
        return $this->customerService->destroy($customer);
    }

    public function edit(CustomerRequest $request, Customer $customer)
    {
        return $this->customerService->edit($request, $customer);
    }
}