<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Offer extends Model
{
    protected $fillable = [
        'customer_id',
        'status_id',
        'total_price',
        'created_by',
        'changed_by',
    ];

    protected $casts = [
        'tool_price' => 'float',
    ];

    public function customer(): BelongsTo 
    {
        return $this->belongsTo(Customer::class);
    }

    public function status(): BelongsTo
    {
        return $this->belongsTo(Status::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    public function offerDetails(): HasMany
    {
        return $this->hasMany(OfferDetail::class);
    }
}
