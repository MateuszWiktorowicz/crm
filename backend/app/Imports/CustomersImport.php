<?php

namespace App\Imports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\Log;

class CustomersImport implements ToModel, WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $existingCustomer = Customer::where('code', $row['code']) ->orwhere('nip', $row['nip'])->first();
        
        if ($existingCustomer) {
            $existingCustomer->update([
                'code' => $row['code'],
                'name' => $row['name'],
                'nip' => $row['nip'],
                'zip_code' => $row['zip_code'],
                'city' => $row['city'],
                'address' => $row['address'],
                'saler_marker' => $row['saler_marker'],
                'description' => $row['description'],
            ]);
        } else {
            return new Customer([
                'code' => $row['code'],
                'name' => $row['name'],
                'nip' => $row['nip'],
                'zip_code' => $row['zip_code'],
                'city' => $row['city'],
                'address' => $row['address'],
                'saler_marker' => $row['saler_marker'],
                'description' => $row['description'],
            ]);
        }
    }
}
