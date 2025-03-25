<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfferDetail extends Model
{
    protected $fillable = [
        'offer_id',
        'tool_geometry_id',
        'quantity',
        'discount',
        'tool_net_price',
        'coating_price_id',
        'coating_net_price',
        'radius',
        "regrinding_option"
    ];

    protected $casts = [
        'tool_net_price' => 'float',
        'coating_net_price' => 'float',
        'discount' => 'float',
        'quantity' => 'integer'
    ];

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
}
