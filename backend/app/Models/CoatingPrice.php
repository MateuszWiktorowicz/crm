<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CoatingType;

class CoatingPrice extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'diameter',
        'price',
        'id_coating'
    ];

    protected $casts = [
        'price' => 'float',
    ];
    
    public function coatingType() {
        return $this->belongsTo(CoatingType::class, 'id_coating');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'diameter' => $this->diameter,
            'price' => round($this->price / 0.6, 2),
            'coatingType' => $this->coatingType ? [
                'id' => $this->coatingType->id,
                'mastermetName' => $this->coatingType->mastermet_name,
                'mastermetCode' => $this->coatingType->mastermet_code,
                'purpose' => $this->coatingType->purpose,
                'description' => $this->coatingType->description,
            ] : null,
        ];
    }
}
