<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\ToolGeometry;

class ToolType extends Model
{
    protected $fillable = [
        'tool_type_name'
    ];

    public function toolGeometries() 
    {
        return $this->hasMany(ToolGeometry::class, 'id_tool_type');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'toolTypeName' => $this->tool_type_name,
        ];
    }
}
