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

    public function coatingType() {
        return $this->belongsTo(CoatingType::class, 'id_coating');
    }
}
