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
    $toolTypes = ToolType::orderBy('tool_type_name', 'asc')->get();
    $files = Tool::All();
    $tools = ToolGeometry::with('toolType')
        ->orderBy('flutes_number', 'asc')
        ->get();
        
    return response()->json([
        'toolTypes' => $toolTypes, 
        'tools' => $tools,
        'files' => $files
        ]);
    }
}
