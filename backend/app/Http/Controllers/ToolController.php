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
    $tools = DB::table('tool_geometries')
        ->join('tool_types', 'tool_geometries.id_tool_type', '=', 'tool_types.id')
        ->select(
            'tool_geometries.id',
            'tool_geometries.flutes_number',
            'tool_geometries.diameter',
            'tool_types.tool_type_name',
            DB::raw("FORMAT(tool_geometries.face_grinding_time * 5, 2, 'pl_PL') AS face_grinding_price"),
            DB::raw("FORMAT(tool_geometries.periphery_grinding_time_2d_tool * 5, 2, 'pl_PL') AS periphery_grinding_price"),
            DB::raw("
                CASE 
                    WHEN tool_geometries.periphery_grinding_time_2d_tool IS NOT NULL 
                    THEN 'face_regrinding,full_regrinding'
                    ELSE 'face_regrinding'
                END AS regrinding_options
            ")
        )
        ->get()
        ->map(function ($tool) {
            $options = explode(',', $tool->regrinding_options);

            $tool->regrinding_options = array_map(function ($option) {
                return [
                    'key' => $option,
                    'label' => $option === 'face_regrinding' ? 'Ostrzenie czoła' : 'Ostrzenie komplet'
                ];
            }, $options);

            return $tool;
        });

        $files = Tool::All();

        return response()->json([
            'toolTypes' => $toolTypes, 
            'tools' => $tools,
            'files' => $files
        ]);
    }
}
