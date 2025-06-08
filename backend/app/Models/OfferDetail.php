<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferDetail extends Model
{
    protected $fillable = [
        'offer_id',
        'tool_type_id',
        'tool_geometry_id',
        'quantity',
        'discount',
        'tool_net_price',
        'coating_price_id',
        'coating_net_price',
        'radius',
        'regrinding_option',
        'description',
        'symbol',
        'file_id', 
    ];

    protected $casts = [
        'tool_net_price' => 'float',
        'coating_net_price' => 'float',
        'discount' => 'float',
        'quantity' => 'integer',
    ];

    protected $hidden = ['created_at', 'updated_at', 'file_id', 'coating_price_id', 'tool_type_id', 'tool_geometry_id'];

    public function offer(): BelongsTo
    {
        return $this->belongsTo(Offer::class);
    }

    public function toolGeometry(): BelongsTo
    {
        return $this->belongsTo(ToolGeometry::class);
    }

    public function coatingPrice(): BelongsTo
    {
        return $this->belongsTo(CoatingPrice::class, 'coating_price_id');
    }

    public function toolType()
    {
        return $this->belongsTo(ToolType::class, 'tool_type_id');
    }

    public function tool(): BelongsTo
    {
        return $this->belongsTo(Tool::class, 'file_id');
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'offerId' => $this->offer_id,
            'quantity' => $this->quantity,
            'discount' => $this->discount,
            'toolNetPrice' => $this->tool_net_price,
            'coatingNetPrice' => $this->coating_net_price,
            'radius' => $this->radius,
            'regrindingOption' => $this->regrinding_option,
            'description' => $this->description,
            'symbol' => $this->symbol,

            'coatingPrice' => $this->coatingPrice ? $this->coatingPrice->toArray() : null,
            'toolType' => $this->toolType ? $this->toolType->toArray() : null,
            'toolGeometry' => $this->toolGeometry ? $this->toolGeometry->toArray() : null,
            'tool' => $this->tool ? $this->tool->toArray() : null,
        ];
    }
}
