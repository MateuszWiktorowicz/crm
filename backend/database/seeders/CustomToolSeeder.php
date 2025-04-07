<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ToolGeometry;

class CustomToolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ToolGeometry::create([
            'flutes_number' => 0,
            'diameter' => 0,
            'face_grinding_time' => 0,
            'periphery_grinding_time_2d_tool' => 0,
            'id_tool_type' => 21,
        ]);
    }
}

