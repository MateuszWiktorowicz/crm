<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\ToolType;

class ToolTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $toolTypes = [
            'Frez Walcowy',
            'Frez Promieniowy',
            'Frez Kulowy',
            'Fazownik',
            'Wiertlo Krete',
            'Niestandardowe',
            'Kartoteka',
            'Frez Zgrubny'
        ];

        foreach ($toolTypes as $toolType) {
            ToolType::firstOrCreate(
                ['tool_type_name' => $toolType]
            );
        }
    }
}
