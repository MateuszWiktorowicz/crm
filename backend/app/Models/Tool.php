<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'price', 'diameter', 'coating_type_id', 'face_grinding_price', 'full_grinding_price'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'price' => 'float',
        'face_grinding_price' => 'float',
        'full_grinding_price' => 'float',
    ];

    public function coatingType()
    {
        return $this->belongsTo(CoatingType::class, 'coating_type_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'price' => $this->price,
            'diameter' => $this->diameter,
            'coatingType' => $this->coatingType ? [
                'id' => $this->coatingType->id,
                'mastermetCode' => $this->coatingType->mastermet_code,
                'mastermetName' => $this->coatingType->mastermet_name,
            ] : null,
            'faceGrindingPrice' => $this->face_grinding_price,
            'fullGrindingPrice' => $this->full_grinding_price,
        ];
    }
}
