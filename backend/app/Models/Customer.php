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

    protected $hidden = ['created_at', 'updated_at'];

    public function offers()
    {
        return $this->hasMany(Offer::class);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'nip' => $this->nip,
            'zipCode' => $this->zip_code,
            'city' => $this->city,
            'address' => $this->address,
            'salerMarker' => $this->saler_marker,
            'description' => $this->description,
        ];
    }
}
