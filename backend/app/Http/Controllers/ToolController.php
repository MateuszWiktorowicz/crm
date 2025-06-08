<?php

namespace App\Http\Controllers;

use App\Models\ToolGeometry;
use Illuminate\Http\Request;
use App\Models\ToolType;
use Illuminate\Support\Facades\DB;
use App\Models\Tool;

class ToolController extends Controller
{
public function index()
{
    $toolTypes = ToolType::all();


    $files = Tool::All();
    $tools = ToolGeometry::with('toolType')->get();

    return response()->json([
        'toolTypes' => $toolTypes, 
        'tools' => $tools,
        'files' => $files
        ]);
    }
}
