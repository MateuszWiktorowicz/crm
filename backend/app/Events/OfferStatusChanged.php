<?php

namespace App\Events;

use App\Models\Offer;
use App\Models\Status;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class OfferStatusChanged
{
    use Dispatchable, SerializesModels;

    public Offer $offer;
    public Status $oldStatus;
    public Status $newStatus;

    /**
     * Create a new event instance.
     */
    public function __construct(Offer $offer, Status $oldStatus, Status $newStatus)
    {
        $this->offer = $offer;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }
}
