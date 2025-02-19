<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Offer;

class OfferController extends Controller
{
    public function index(Request $request) {
        $offers = Offer::all();

        return response()->json([
            'offers' => $offers,
        ]);
    }
}
