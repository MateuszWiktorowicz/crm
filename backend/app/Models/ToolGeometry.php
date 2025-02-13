<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ToolType;

class ToolGeometry extends Model
{
    protected $fillable = [
        'flutes_number',
        'diameter',
        'face_grinding_time',
        'periphery_grinding_times_2d_tool',
        'id_tool_type'
    ];

    public function toolType() 
    {
        return $this->belongsTo(ToolType::class, 'id_tool_type');
    } 
    
}
