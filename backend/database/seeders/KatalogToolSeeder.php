<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tool;

class KatalogToolSeeder extends Seeder
{
    public function run(): void
    {
        $tools = [
            // ECO coating
            ['code' => 'MM04.51.4',         'name' => 'MM04.51.4',         'price' => null, 'diameter' => 4,  'coating_type_id' => 10,          'face_grinding_price' => 30.08, 'full_grinding_price' => 43.58],
            ['code' => 'MM05.55.4',         'name' => 'MM05.55.4',         'price' => null, 'diameter' => 5,  'coating_type_id' => 10,          'face_grinding_price' => 30.08, 'full_grinding_price' => 43.58],
            ['code' => 'MM06.55.4',         'name' => 'MM06.55.4',         'price' => null, 'diameter' => 6,  'coating_type_id' => 10,          'face_grinding_price' => 34.58, 'full_grinding_price' => 48.08],
            ['code' => 'MM08.59.4',         'name' => 'MM08.59.4',         'price' => null, 'diameter' => 8,  'coating_type_id' => 10,          'face_grinding_price' => 34.08, 'full_grinding_price' => 52.08],
            ['code' => 'MM10.73.4',         'name' => 'MM10.73.4',         'price' => null, 'diameter' => 10, 'coating_type_id' => 10,          'face_grinding_price' => 38.12, 'full_grinding_price' => 69.62],
            ['code' => 'MM12.74.4',         'name' => 'MM12.74.4',         'price' => null, 'diameter' => 12, 'coating_type_id' => 10,          'face_grinding_price' => 42.18, 'full_grinding_price' => 73.68],
            ['code' => 'MM14.76.4',         'name' => 'MM14.76.4',         'price' => null, 'diameter' => 14, 'coating_type_id' => 10,          'face_grinding_price' => 41.87, 'full_grinding_price' => 77.87],
            ['code' => 'MM16.83.4',         'name' => 'MM16.83.4',         'price' => null, 'diameter' => 16, 'coating_type_id' => 10,          'face_grinding_price' => 45.92, 'full_grinding_price' => 77.42],
            ['code' => 'MM18.93.4',         'name' => 'MM18.93.4',         'price' => null, 'diameter' => 18, 'coating_type_id' => 10,          'face_grinding_price' => 45.45, 'full_grinding_price' => 81.45],
            ['code' => 'MM20.105.4',        'name' => 'MM20.105.4',        'price' => null, 'diameter' => 20, 'coating_type_id' => 10,          'face_grinding_price' => 49.52, 'full_grinding_price' => 85.52],
            // TripleCr Z3 BL
            ['code' => 'MM03.60.3.BL',      'name' => 'MM03.60.3.BL',      'price' => null, 'diameter' => 3,  'coating_type_id' => 3,     'face_grinding_price' => 29.18, 'full_grinding_price' => 38.18],
            ['code' => 'MM04.60.3.BL',      'name' => 'MM04.60.3.BL',      'price' => null, 'diameter' => 4,  'coating_type_id' => 3,     'face_grinding_price' => 29.18, 'full_grinding_price' => 38.18],
            ['code' => 'MM05.55.3.BL',      'name' => 'MM05.55.3.BL',      'price' => null, 'diameter' => 5,  'coating_type_id' => 3,     'face_grinding_price' => 29.18, 'full_grinding_price' => 38.18],
            ['code' => 'MM06.55.3.BL',      'name' => 'MM06.55.3.BL',      'price' => null, 'diameter' => 6,  'coating_type_id' => 3,     'face_grinding_price' => 33.68, 'full_grinding_price' => 42.68],
            ['code' => 'MM07.59.3.BL',      'name' => 'MM07.59.3.BL',      'price' => null, 'diameter' => 7,  'coating_type_id' => 3,     'face_grinding_price' => 32.87, 'full_grinding_price' => 46.37],
            ['code' => 'MM08.59.3.BL',      'name' => 'MM08.59.3.BL',      'price' => null, 'diameter' => 8,  'coating_type_id' => 3,     'face_grinding_price' => 32.87, 'full_grinding_price' => 46.37],
            ['code' => 'MM09.73.3.BL',      'name' => 'MM09.73.3.BL',      'price' => null, 'diameter' => 9,  'coating_type_id' => 3,     'face_grinding_price' => 36.58, 'full_grinding_price' => 63.58],
            ['code' => 'MM10.73.3.BL',      'name' => 'MM10.73.3.BL',      'price' => null, 'diameter' => 10, 'coating_type_id' => 3,     'face_grinding_price' => 36.58, 'full_grinding_price' => 63.58],
            ['code' => 'MM12.74.3.BL',      'name' => 'MM12.74.3.BL',      'price' => null, 'diameter' => 12, 'coating_type_id' => 3,     'face_grinding_price' => 40.32, 'full_grinding_price' => 67.32],
            ['code' => 'MM16.83.3.BL',      'name' => 'MM16.83.3.BL',      'price' => null, 'diameter' => 16, 'coating_type_id' => 3,     'face_grinding_price' => 43.60, 'full_grinding_price' => 70.60],
            // TripleCr Z4 BL
            ['code' => 'MM04.51.4.BL',      'name' => 'MM04.51.4.BL',      'price' => null, 'diameter' => 4,  'coating_type_id' => 3,     'face_grinding_price' => 29.18, 'full_grinding_price' => 42.68],
            ['code' => 'MM06.55.4.BL',      'name' => 'MM06.55.4.BL',      'price' => null, 'diameter' => 6,  'coating_type_id' => 3,     'face_grinding_price' => 33.68, 'full_grinding_price' => 47.18],
            ['code' => 'MM08.59.4.BL',      'name' => 'MM08.59.4.BL',      'price' => null, 'diameter' => 8,  'coating_type_id' => 3,     'face_grinding_price' => 32.87, 'full_grinding_price' => 50.87],
            ['code' => 'MM10.73.4.BL',      'name' => 'MM10.73.4.BL',      'price' => null, 'diameter' => 10, 'coating_type_id' => 3,     'face_grinding_price' => 36.58, 'full_grinding_price' => 68.08],
            ['code' => 'MM12.74.4.BL',      'name' => 'MM12.74.4.BL',      'price' => null, 'diameter' => 12, 'coating_type_id' => 3,     'face_grinding_price' => 40.32, 'full_grinding_price' => 71.82],
            ['code' => 'MM14.76.4.BL',      'name' => 'MM14.76.4.BL',      'price' => null, 'diameter' => 14, 'coating_type_id' => 3,     'face_grinding_price' => 39.83, 'full_grinding_price' => 75.83],
            ['code' => 'MM16.83.4.BL',      'name' => 'MM16.83.4.BL',      'price' => null, 'diameter' => 16, 'coating_type_id' => 3,     'face_grinding_price' => 43.60, 'full_grinding_price' => 75.10],
            ['code' => 'MM18.93.4.BL',      'name' => 'MM18.93.4.BL',      'price' => null, 'diameter' => 18, 'coating_type_id' => 3,     'face_grinding_price' => 42.87, 'full_grinding_price' => 78.87],
            ['code' => 'MM20.105.4.BL',     'name' => 'MM20.105.4.BL',     'price' => null, 'diameter' => 20, 'coating_type_id' => 3,     'face_grinding_price' => 46.65, 'full_grinding_price' => 82.65],
            // TripleCr Z5 BL
            ['code' => 'MM04.51.5.BL',      'name' => 'MM04.51.5.BL',      'price' => null, 'diameter' => 4,  'coating_type_id' => 3,     'face_grinding_price' => 33.68, 'full_grinding_price' => 51.68],
            ['code' => 'MM06.55.5.BL',      'name' => 'MM06.55.5.BL',      'price' => null, 'diameter' => 6,  'coating_type_id' => 3,     'face_grinding_price' => 38.18, 'full_grinding_price' => 56.18],
            ['code' => 'MM08.59.5.BL',      'name' => 'MM08.59.5.BL',      'price' => null, 'diameter' => 8,  'coating_type_id' => 3,     'face_grinding_price' => 37.37, 'full_grinding_price' => 68.87],
            ['code' => 'MM10.73.5.BL',      'name' => 'MM10.73.5.BL',      'price' => null, 'diameter' => 10, 'coating_type_id' => 3,     'face_grinding_price' => 41.08, 'full_grinding_price' => 86.08],
            ['code' => 'MM12.74.5.BL',      'name' => 'MM12.74.5.BL',      'price' => null, 'diameter' => 12, 'coating_type_id' => 3,     'face_grinding_price' => 40.32, 'full_grinding_price' => 89.82],
            ['code' => 'MM14.76.5.BL',      'name' => 'MM14.76.5.BL',      'price' => null, 'diameter' => 14, 'coating_type_id' => 3,     'face_grinding_price' => 44.33, 'full_grinding_price' => 89.33],
            ['code' => 'MM16.83.5.BL',      'name' => 'MM16.83.5.BL',      'price' => null, 'diameter' => 16, 'coating_type_id' => 3,     'face_grinding_price' => 43.60, 'full_grinding_price' => 93.10],
            ['code' => 'MM18.93.5.BL',      'name' => 'MM18.93.5.BL',      'price' => null, 'diameter' => 18, 'coating_type_id' => 3,     'face_grinding_price' => 47.37, 'full_grinding_price' => 96.87],
            ['code' => 'MM20.105.5.BL',     'name' => 'MM20.105.5.BL',     'price' => null, 'diameter' => 20, 'coating_type_id' => 3,     'face_grinding_price' => 46.65, 'full_grinding_price' => 100.65],
            // AL Z1 (no coating)
            ['code' => 'MM03.51.1.AL',      'name' => 'MM03.51.1.AL',      'price' => null, 'diameter' => 3,  'coating_type_id' => null,           'face_grinding_price' => 27.00, 'full_grinding_price' => 36.00],
            ['code' => 'MM04.60.1.AL',      'name' => 'MM04.60.1.AL',      'price' => null, 'diameter' => 4,  'coating_type_id' => null,           'face_grinding_price' => 27.00, 'full_grinding_price' => 36.00],
            ['code' => 'MM05.60.1.AL',      'name' => 'MM05.60.1.AL',      'price' => null, 'diameter' => 5,  'coating_type_id' => null,           'face_grinding_price' => 27.00, 'full_grinding_price' => 36.00],
            ['code' => 'MM06.65.1.AL',      'name' => 'MM06.65.1.AL',      'price' => null, 'diameter' => 6,  'coating_type_id' => null,           'face_grinding_price' => 31.50, 'full_grinding_price' => 40.50],
            ['code' => 'MM08.64.1.AL',      'name' => 'MM08.64.1.AL',      'price' => null, 'diameter' => 8,  'coating_type_id' => null,           'face_grinding_price' => 31.50, 'full_grinding_price' => 45.00],
            ['code' => 'MM10.80.1.AL',      'name' => 'MM10.80.1.AL',      'price' => null, 'diameter' => 10, 'coating_type_id' => null,           'face_grinding_price' => 36.00, 'full_grinding_price' => 58.50],
            ['code' => 'MM12.84.1.AL',      'name' => 'MM12.84.1.AL',      'price' => null, 'diameter' => 12, 'coating_type_id' => null,           'face_grinding_price' => 36.00, 'full_grinding_price' => 63.00],
            // AL Z2 (no coating)
            ['code' => 'MM03.50.2.AL',      'name' => 'MM03.50.2.AL',      'price' => null, 'diameter' => 3,  'coating_type_id' => null,           'face_grinding_price' => 27.00, 'full_grinding_price' => 36.00],
            ['code' => 'MM04.50.2.AL',      'name' => 'MM04.50.2.AL',      'price' => null, 'diameter' => 4,  'coating_type_id' => null,           'face_grinding_price' => 27.00, 'full_grinding_price' => 36.00],
            ['code' => 'MM05.55.2.AL',      'name' => 'MM05.55.2.AL',      'price' => null, 'diameter' => 5,  'coating_type_id' => null,           'face_grinding_price' => 27.00, 'full_grinding_price' => 36.00],
            ['code' => 'MM06.55.2.AL',      'name' => 'MM06.55.2.AL',      'price' => null, 'diameter' => 6,  'coating_type_id' => null,           'face_grinding_price' => 31.50, 'full_grinding_price' => 40.50],
            ['code' => 'MM08.63.2.AL',      'name' => 'MM08.63.2.AL',      'price' => null, 'diameter' => 8,  'coating_type_id' => null,           'face_grinding_price' => 31.50, 'full_grinding_price' => 45.00],
            ['code' => 'MM10.70.2.AL',      'name' => 'MM10.70.2.AL',      'price' => null, 'diameter' => 10, 'coating_type_id' => null,           'face_grinding_price' => 36.00, 'full_grinding_price' => 58.50],
            ['code' => 'MM12.74.2.AL',      'name' => 'MM12.74.2.AL',      'price' => null, 'diameter' => 12, 'coating_type_id' => null,           'face_grinding_price' => 36.00, 'full_grinding_price' => 63.00],
            ['code' => 'MM14.76.2.AL',      'name' => 'MM14.76.2.AL',      'price' => null, 'diameter' => 14, 'coating_type_id' => null,           'face_grinding_price' => 40.50, 'full_grinding_price' => 67.50],
            ['code' => 'MM16.83.2.AL',      'name' => 'MM16.83.2.AL',      'price' => null, 'diameter' => 16, 'coating_type_id' => null,           'face_grinding_price' => 40.50, 'full_grinding_price' => 67.50],
            ['code' => 'MM18.93.2.AL',      'name' => 'MM18.93.2.AL',      'price' => null, 'diameter' => 18, 'coating_type_id' => null,           'face_grinding_price' => 45.00, 'full_grinding_price' => 72.00],
            ['code' => 'MM20.105.2.AL',     'name' => 'MM20.105.2.AL',     'price' => null, 'diameter' => 20, 'coating_type_id' => null,           'face_grinding_price' => 49.50, 'full_grinding_price' => 76.50],
            // AL Z3 (no coating)
            ['code' => 'MM04.51.3.AL',      'name' => 'MM04.51.3.AL',      'price' => null, 'diameter' => 4,  'coating_type_id' => null,           'face_grinding_price' => 31.50, 'full_grinding_price' => 40.50],
            ['code' => 'MM06.55.3.AL',      'name' => 'MM06.55.3.AL',      'price' => null, 'diameter' => 6,  'coating_type_id' => null,           'face_grinding_price' => 36.00, 'full_grinding_price' => 45.00],
            ['code' => 'MM08.59.3.AL',      'name' => 'MM08.59.3.AL',      'price' => null, 'diameter' => 8,  'coating_type_id' => null,           'face_grinding_price' => 36.00, 'full_grinding_price' => 49.50],
            ['code' => 'MM10.73.3.AL',      'name' => 'MM10.73.3.AL',      'price' => null, 'diameter' => 10, 'coating_type_id' => null,           'face_grinding_price' => 40.50, 'full_grinding_price' => 67.50],
            ['code' => 'MM12.74.3.AL',      'name' => 'MM12.74.3.AL',      'price' => null, 'diameter' => 12, 'coating_type_id' => null,           'face_grinding_price' => 45.00, 'full_grinding_price' => 72.00],
            ['code' => 'MM14.76.3.AL',      'name' => 'MM14.76.3.AL',      'price' => null, 'diameter' => 14, 'coating_type_id' => null,           'face_grinding_price' => 45.00, 'full_grinding_price' => 76.50],
            ['code' => 'MM16.83.3.AL',      'name' => 'MM16.83.3.AL',      'price' => null, 'diameter' => 16, 'coating_type_id' => null,           'face_grinding_price' => 49.50, 'full_grinding_price' => 76.50],
            ['code' => 'MM18.93.3.AL',      'name' => 'MM18.93.3.AL',      'price' => null, 'diameter' => 18, 'coating_type_id' => null,           'face_grinding_price' => 49.50, 'full_grinding_price' => 81.00],
            ['code' => 'MM20.105.3.AL',     'name' => 'MM20.105.3.AL',     'price' => null, 'diameter' => 20, 'coating_type_id' => null,           'face_grinding_price' => 54.00, 'full_grinding_price' => 85.50],
            // TripleCr + Z (radius end mills R0.5)
            ['code' => 'MM04.51.4.R0.5.HM', 'name' => 'MM04.51.4.R0.5.HM', 'price' => null, 'diameter' => 4,  'coating_type_id' => 3, 'face_grinding_price' => 47.18, 'full_grinding_price' => 83.18],
            ['code' => 'MM06.55.4.R0.5.HM', 'name' => 'MM06.55.4.R0.5.HM', 'price' => null, 'diameter' => 6,  'coating_type_id' => 3, 'face_grinding_price' => 47.18, 'full_grinding_price' => 83.18],
            ['code' => 'MM08.59.4.R0.5.HM', 'name' => 'MM08.59.4.R0.5.HM', 'price' => null, 'diameter' => 8,  'coating_type_id' => 3, 'face_grinding_price' => 50.87, 'full_grinding_price' => 91.37],
            ['code' => 'MM10.73.4.R0.5.HM', 'name' => 'MM10.73.4.R0.5.HM', 'price' => null, 'diameter' => 10, 'coating_type_id' => 3, 'face_grinding_price' => 43.03, 'full_grinding_price' => 95.08],
            ['code' => 'MM12.74.4.R0.5.HM', 'name' => 'MM12.74.4.R0.5.HM', 'price' => null, 'diameter' => 12, 'coating_type_id' => 3, 'face_grinding_price' => 53.82, 'full_grinding_price' => 103.32],
            ['code' => 'MM14.76.4.R0.5.HM', 'name' => 'MM14.76.4.R0.5.HM', 'price' => null, 'diameter' => 14, 'coating_type_id' => 3, 'face_grinding_price' => 57.83, 'full_grinding_price' => 111.83],
            ['code' => 'MM16.83.4.R0.5.HM', 'name' => 'MM16.83.4.R0.5.HM', 'price' => null, 'diameter' => 16, 'coating_type_id' => 3, 'face_grinding_price' => 61.60, 'full_grinding_price' => 129.10],
            ['code' => 'MM18.93.4.R0.5.HM', 'name' => 'MM18.93.4.R0.5.HM', 'price' => null, 'diameter' => 18, 'coating_type_id' => 3, 'face_grinding_price' => 65.37, 'full_grinding_price' => 137.37],
            ['code' => 'MM20.105.4.R0.5.HM','name' => 'MM20.105.4.R0.5.HM','price' => null, 'diameter' => 20, 'coating_type_id' => 3, 'face_grinding_price' => 64.65, 'full_grinding_price' => 141.15],
            // MarwinG 60°
            ['code' => 'MC06.54.60°',       'name' => 'MC06.54.60°',       'price' => null, 'diameter' => 6,  'coating_type_id' => 1,      'face_grinding_price' => 43.58, 'full_grinding_price' => null],
            ['code' => 'MC08.59.60°',       'name' => 'MC08.59.60°',       'price' => null, 'diameter' => 8,  'coating_type_id' => 1,      'face_grinding_price' => 43.08, 'full_grinding_price' => null],
            ['code' => 'MC10.60.60°',       'name' => 'MC10.60.60°',       'price' => null, 'diameter' => 10, 'coating_type_id' => 1,      'face_grinding_price' => 42.62, 'full_grinding_price' => null],
            ['code' => 'MC12.66.60°',       'name' => 'MC12.66.60°',       'price' => null, 'diameter' => 12, 'coating_type_id' => 1,      'face_grinding_price' => 51.18, 'full_grinding_price' => null],
            // MarwinG 90°
            ['code' => 'MC06.54.90°',       'name' => 'MC06.54.90°',       'price' => null, 'diameter' => 6,  'coating_type_id' => 1,      'face_grinding_price' => 43.58, 'full_grinding_price' => null],
            ['code' => 'MC08.59.90°',       'name' => 'MC08.59.90°',       'price' => null, 'diameter' => 8,  'coating_type_id' => 1,      'face_grinding_price' => 43.08, 'full_grinding_price' => null],
            ['code' => 'MC10.60.90°',       'name' => 'MC10.60.90°',       'price' => null, 'diameter' => 10, 'coating_type_id' => 1,      'face_grinding_price' => 42.62, 'full_grinding_price' => null],
            ['code' => 'MC12.66.90°',       'name' => 'MC12.66.90°',       'price' => null, 'diameter' => 12, 'coating_type_id' => 1,      'face_grinding_price' => 51.18, 'full_grinding_price' => null],
            // Fixed-price kartoteka items
            ['code' => 'opakowanie',        'name' => 'opakowanie',        'price' => 2.00, 'diameter' => null, 'coating_type_id' => null,  'face_grinding_price' => null,  'full_grinding_price' => null],
            ['code' => 'szlifowanie-obnizenia', 'name' => 'szlifowanie obniżenia', 'price' => 0.00, 'diameter' => null, 'coating_type_id' => null, 'face_grinding_price' => null, 'full_grinding_price' => null],
        ];

        foreach ($tools as $tool) {
            Tool::firstOrCreate(
                ['code' => $tool['code']],
                $tool
            );
        }
    }
}
