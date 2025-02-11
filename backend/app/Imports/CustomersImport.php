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
        $existingCustomer = Customer::where('code', $row['kod']) ->orwhere('nip', $row['nip'])->first();
        
        if ($existingCustomer) {
            $existingCustomer->update([
                'code' => $row['kod'],
                'name' => $row['nazwa'],
                'nip' => $row['nip'],
                'zip_code' => $row['kod_pocztowy'],
                'city' => $row['miasto'],
                'address' => $row['ulica'],
                'saler_marker' => $row['grupa'],
                'description' => $row['uwagi'],
            ]);
        } else {
            return new Customer([
                'code' => $row['kod'],
                'name' => $row['nazwa'],
                'nip' => $row['nip'],
                'zip_code' => $row['kod_pocztowy'],
                'city' => $row['miasto'],
                'address' => $row['ulica'],
                'saler_marker' => $row['grupa'],
                'description' => $row['uwagi'],
            ]);
        }
    }
}
