<?php

namespace App\Events;

use App\Models\Offer;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferNeedsReview
{
    use Dispatchable, SerializesModels;

    public Offer $offer;

    /**
     * Create a new event instance.
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
    }
}
