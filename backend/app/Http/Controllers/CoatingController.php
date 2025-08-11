<?php

namespace App\Http\Controllers;

use App\Models\CoatingPrice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CoatingType;

class CoatingController extends Controller
{
    public function index()
    {
        $coatingTypes = CoatingType::orderBy('mastermet_code', 'asc')->get();
        $coatings = CoatingPrice::with('coatingType')->get();

        return response()->json([
            'coatingTypes' => $coatingTypes, 
            'coatings' => $coatings,
        ]);
    }
}
