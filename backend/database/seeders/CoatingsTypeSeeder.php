<?php

namespace Database\Seeders;

use App\Models\CoatingType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CoatingsTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['description' => 'TiAlN+AlTiN', 'mastermet_name' => 'MarwinG', 'mastermet_code' => 'MM01'],
            ['description' => 'CrAlSiN', 'mastermet_name' => 'Alwin', 'mastermet_code' => 'MM03'],
            ['description' => 'TiN+AlTiN+CrAlSiN', 'mastermet_name' => 'TrippleCr', 'mastermet_code' => 'MM04'],
            ['description' => 'AlTiN+CrAlSiN+ZrN', 'mastermet_name' => 'TrippleZrN', 'mastermet_code' => 'MM05'],
            ['description' => 'TiN+AlTiN+TiSiN', 'mastermet_name' => 'TrippleSi', 'mastermet_code' => 'MM06'],
            ['description' => 'AlCrBN', 'mastermet_name' => 'BIGAAN', 'mastermet_code' => 'MM07'],
            ['description' => 'TiN', 'mastermet_name' => 'TiN', 'mastermet_code' => 'MM12'],
            ['description' => 'ZrN', 'mastermet_name' => 'ZrN', 'mastermet_code' => 'MM08'],
            ['description' => 'Biggan XC', 'mastermet_name' => 'Biggan XC', 'mastermet_code' => 'MM09'],
            ['description' => 'ECO', 'mastermet_name' => 'ECO', 'mastermet_code' => 'MM11'],
            ['description' => 'Biggan Si', 'mastermet_name' => 'Biggan Si', 'mastermet_code' => 'MM13'],




        ];

        foreach ($data as $item) {
            CoatingType::updateOrCreate(
                ['mastermet_name' => $item['mastermet_name']],
                [
                    'description'     => $item['description'],
                    'mastermet_code'  => $item['mastermet_code'],
                    'purpose'         => '',
                ]
            );
        }
    }
    }

