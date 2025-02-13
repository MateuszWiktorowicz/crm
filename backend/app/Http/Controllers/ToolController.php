<?php

namespace App\Http\Controllers;

use App\Models\ToolGeometry;
use Illuminate\Http\Request;
use App\Models\ToolType;
use Illuminate\Support\Facades\DB;

class ToolController extends Controller
{
    public function index()
    {
        $toolTypes = ToolType::all();
        $tools = DB::table('tool_geometries')
            ->join('tool_types', 'tool_geometries.id_tool_type', '=', 'tool_types.id')
            ->select(
                'tool_geometries.id',
                'tool_geometries.flutes_number',
                'tool_geometries.diameter', 
                'tool_types.tool_type_name',
                DB::raw('tool_geometries.face_grinding_time * 5 as face_grinding_price'),
                DB::raw('tool_geometries.periphery_grinding_time_2d_tool * 5 as periphery_grinding_price')
                )
            ->get();

        return response()->json([
            'toolTypes' => $toolTypes, 
            'tools' => $tools,
        ]);
    }
}
