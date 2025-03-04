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
        'tool_quantity',
        'tool_discount',
        'tool_total_net_price',
        'tool_total_gross_price',
        'coating_price_id',
        'coating_quantity',
        'coating_discount',
        'coating_net_price',
        'coating_gross_price',
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
