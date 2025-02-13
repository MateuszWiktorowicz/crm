<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ToolType;

class ToolController extends Controller
{
    public function index()
    {
        $toolTypes = ToolType::all();

        return response()->json([
            'toolTypes' => $toolTypes,
        ]);
    }
}
