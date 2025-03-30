<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            'Niestandardowe'
        ];

        foreach ($toolTypes as $toolType) {
            DB::table('tool_types')->insert([
                'tool_type_name' => $toolType
            ]);
        };
    }
}
