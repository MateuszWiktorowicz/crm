<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'code',
        'name',
        'nip',
        'zip_code',
        'city',
        'address',
        'saler_marker',
        'description',
    ];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

}
