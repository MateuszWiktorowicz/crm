<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Settings extends Model
{
    protected $fillable = [
        'offer_number'
    ];

    public function toArray()
    {
        return [
            'id' => $this->id,
            'offerNumber' => $this->offer_number,
        ];
    }
}