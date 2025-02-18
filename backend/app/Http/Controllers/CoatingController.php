<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CoatingType;

class CoatingController extends Controller
{
    public function index()
    {
        $coatingTypes = CoatingType::all();
        $coatings = DB::table('coating_types')
            ->join('coating_prices', 'coating_prices.id_coating', '=', 'coating_types.id')
            ->select(
                'coating_prices.id',
                'coating_types.mastermet_name',
                'coating_types.mastermet_code',
                'coating_prices.price',
                'coating_prices.diameter', 
                )
            ->get();

        return response()->json([
            'coatingTypes' => $coatingTypes, 
            'coatings' => $coatings,
        ]);
    }
}
