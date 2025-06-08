<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CoatingPrice;

class CoatingType extends Model
{
    public $timestamps = false;
    
    protected $fillable = [
        'mastermet_name',
        'mastermet_code',
        'purpose',
        'description',
    ];

    public function coatingPrices()
    {
        return $this->hasMany(CoatingPrice::class, 'id_coating');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'mastermetName' => $this->mastermet_name,
            'mastermetCode' => $this->mastermet_code,
            'purpose' => $this->purpose,
            'description' => $this->description,
        ];
    }
}
