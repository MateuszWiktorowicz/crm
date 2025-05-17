<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tool;

class ToolSeeder extends Seeder
{
    public function run()
    {
        $tools = [
            ['code' => 'MM96-08 (zrn)', 'name' => 'MM96-08 (zrn)', 'price' => 40.79, 'diameter' => 8],
            ['code' => 'MM112-10 (zrn)', 'name' => 'MM112-10 (zrn)', 'price' => 58.04, 'diameter' => 10],
            ['code' => 'MM111-16 (zrn)', 'name' => 'MM111-16 (zrn)', 'price' => 78.18, 'diameter' => 16],
            ['code' => 'CMM08.16.8.R0.8 (Triplecoating SI)', 'name' => 'CMM08.16.8.R0.8 (Triplecoating SI)', 'price' => 50.99, 'diameter' => 8],
            ['code' => 'CMM12.26.4.R3 (Triplecoating SI)', 'name' => 'CMM12.26.4.R3 (Triplecoating SI)', 'price' => 69.89, 'diameter' => 12],
            ['code' => 'NRFRFU0423-14 IKZ (MarwinG)', 'name' => 'NRFRFU0423-14 IKZ (MarwinG)', 'price' => 165.00, 'diameter' => 4],
            ['code' => 'MM102-04', 'name' => 'MM102-04', 'price' => 17.00, 'diameter' => 4],
            ['code' => 'H3123118-5', 'name' => 'H3123118-5', 'price' => 18.37, 'diameter' => 4],
            ['code' => 'MM106-06', 'name' => 'MM106-06', 'price' => 18.37, 'diameter' => 6],
            ['code' => 'MM39-06', 'name' => 'MM39-06', 'price' => 19.83, 'diameter' => 6],
            ['code' => 'EMCX-252500-04 075-R30', 'name' => 'EMCX-252500-04 075-R30', 'price' => 18.67, 'diameter' => 6],
            ['code' => 'EMCX-07608M-04 19-R00', 'name' => 'EMCX-07608M-04 19-R00', 'price' => 17.57, 'diameter' => 6],
            ['code' => 'EMCX-07610M-04 22-R00', 'name' => 'EMCX-07610M-04 22-R00', 'price' => 20.14, 'diameter' => 6],
            ['code' => '451100241', 'name' => '451100241', 'price' => 20.14, 'diameter' => 6],
            ['code' => 'MM73-10', 'name' => 'MM73-10', 'price' => 30.59, 'diameter' => 6],
            ['code' => 'H8015828-10-1.5-35', 'name' => 'H8015828-10-1.5-35', 'price' => 30.59, 'diameter' => 6],
            ['code' => 'MM108-10', 'name' => 'MM108-10', 'price' => 30.03, 'diameter' => 6],
            ['code' => 'MM81-10', 'name' => 'MM81-10', 'price' => 30.59, 'diameter' => 6],
            ['code' => 'MM72-38', 'name' => 'MM72-38', 'price' => 30.59, 'diameter' => 6],
            ['code' => 'EMCX-07612M-04 26-R00', 'name' => 'EMCX-07612M-04 26-R00', 'price' => 22.66, 'diameter' => 6],
            ['code' => 'MM74-12', 'name' => 'MM74-12', 'price' => 31.98, 'diameter' => 6],
            ['code' => 'MM110-12', 'name' => 'MM110-12', 'price' => 29.61, 'diameter' => 6],
            ['code' => 'K14536024', 'name' => 'K14536024', 'price' => 28.33, 'diameter' => 6],
            ['code' => 'MM154-12', 'name' => 'MM154-12', 'price' => 96.25, 'diameter' => 6],
            ['code' => 'MM154-12 REG2.', 'name' => 'MM154-12 REG2.', 'price' => 96.25, 'diameter' => 6],
            ['code' => 'MM20-32', 'name' => 'MM20-32', 'price' => 65.71, 'diameter' => 6],
            ['code' => 'MM149-20', 'name' => 'MM149-20', 'price' => 79.31, 'diameter' => 6],
            ['code' => 'MM25-32', 'name' => 'MM25-32', 'price' => 67.98, 'diameter' => 6],
            ['code' => 'N1 445 9280', 'name' => 'N1 445 9280', 'price' => 165.00, 'diameter' => 6],
            ['code' => 'MM82-13', 'name' => 'MM82-13', 'price' => 54.96, 'diameter' => 6],
            ['code' => 'MM47-014', 'name' => 'MM47-014', 'price' => 54.96, 'diameter' => 6],
            ['code' => 'MM94-15', 'name' => 'MM94-15', 'price' => 61.18, 'diameter' => 6],
            ['code' => 'MM46-179', 'name' => 'MM46-179', 'price' => 73.08, 'diameter' => 6],
            ['code' => 'Rozwiertaki', 'name' => 'Rozwiertaki', 'price' => 35.54, 'diameter' => 6],
            ['code' => 'MM99-06/90st. L', 'name' => 'MM99-06/90st. L', 'price' => 26.06, 'diameter' => 6],
            ['code' => 'H3058318-6 6x2.5/90st.', 'name' => 'H3058318-6 6x2.5/90st.', 'price' => 26.06, 'diameter' => 6],
            ['code' => 'N1012', 'name' => 'N1012', 'price' => 130.30, 'diameter' => 6],
            ['code' => 'E1001', 'name' => 'N1012', 'price' => 124.37, 'diameter' => 6],
            ['code' => 'E1002', 'name' => 'E1002', 'price' => 124.37, 'diameter' => 6],
            ['code' => 'E1003', 'name' => 'E1003', 'price' => 124.37, 'diameter' => 6],
            ['code' => 'E1004', 'name' => 'E1004', 'price' => 124.37, 'diameter' => 6],
            ['code' => 'E1009', 'name' => 'E1009', 'price' => 47.27, 'diameter' => 10],
            ['code' => 'E1010', 'name' => 'E1010', 'price' => 31.40, 'diameter' => 10],
            ['code' => 'E1013', 'name' => 'E1013', 'price' => 47.27, 'diameter' => 14],
            ['code' => 'E1014', 'name' => 'E1014', 'price' => 31.40, 'diameter' => 14],
            ['code' => 'E1020', 'name' => 'E1020', 'price' => 17.83, 'diameter' => 20],
        ];

        foreach ($tools as $tool) {
            Tool::firstOrCreate(
                ['code' => $tool['code']],
                $tool
            );
        }
    }
}
