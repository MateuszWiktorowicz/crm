<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ToolType;

class ToolGeometry extends Model
{
    public $timestamps = false;
    protected $fillable = [
        'flutes_number',
        'diameter',
        'face_grinding_time',
        'periphery_grinding_time_2d_tool',
        'id_tool_type'
    ];

    protected $hidden = ['face_grinding_time', 'periphery_grinding_time_2d_tool'];

    public function toolType() 
    {
        return $this->belongsTo(ToolType::class, 'id_tool_type');
    } 
    
public function toArray()
{
    return [
        'id' => $this->id,
        'flutesNumber' => $this->flutes_number,
        'diameter' => $this->diameter,
        'faceGrindingPrice' => round($this->face_grinding_time * 5, 2),
        'peripheryGrindingPrice' => $this->periphery_grinding_time_2d_tool
            ? round($this->periphery_grinding_time_2d_tool * 5, 2)
            : null,
        'toolType' => $this->toolType ? $this->toolType->toArray() : null,
        'regrindingOptions' => collect([
            [
                'key' => 'face_regrinding',
                'label' => 'Ostrzenie czoła',
            ],
            $this->periphery_grinding_time_2d_tool ? [
                'key' => 'full_regrinding',
                'label' => 'Ostrzenie komplet',
            ] : null,
            $this->periphery_grinding_time_2d_tool ? [
                'key' => 'periphery_regrinding',
                'label' => 'Ostrzenie pod zębem',
            ] : null,
        ])->filter()->values(),
    ];
}



}
