<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = ['code', 'name', 'price', 'diameter'];

    protected $hidden = ['created_at', 'updated_at'];

    protected $casts = [
        'price' => 'float',
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'code' => $this->code,
            'name' => $this->name,
            'price' => $this->price,
            'diameter' => $this->diameter,
        ];
    }
}
