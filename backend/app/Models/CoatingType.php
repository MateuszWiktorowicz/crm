<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\CoatingPrice;

class CoatingType extends Model
{
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
}
