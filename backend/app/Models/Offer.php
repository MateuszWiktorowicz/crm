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
        'offer_number'
    ];

    protected $casts = [
        'total_price' => 'float',
    ];

    protected $hidden = ['status_id', 'customer_id'];


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

    public function pdfInfo()
    {
        return $this->hasOne(OfferPdfInfo::class);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'customer' => $this->customer ? $this->customer->toArray() : null,
            'status' => $this->status ? $this->status->toArray() : null,
            'totalPrice' => $this->total_price,
            'createdBy' => $this->createdBy ? $this->createdBy->toArray() : null,
            'changedBy' => $this->changedBy ? $this->changedBy->toArray() : null,
            'offerNumber' => $this->offer_number,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
            'offerDetails' => $this->offerDetails->map(function ($detail) {
                return $detail->toArray();
            })->all(),
            'pdfInfo' => $this->pdfInfo ? $this->pdfInfo->toArray() : null,
        ];
    }
}
