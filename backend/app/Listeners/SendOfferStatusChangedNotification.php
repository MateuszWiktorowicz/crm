<?php

namespace App\Listeners;

use App\Events\OfferStatusChanged;
use App\Notifications\OfferStatusChangedNotification;

class SendOfferStatusChangedNotification
{
    /**
     * Handle the event.
     */
    public function handle(OfferStatusChanged $event): void
    {
        $offer = $event->offer;
        
        // Wysyłamy powiadomienie do handlowca (created_by)
        if ($offer->createdBy) {
            $offer->createdBy->notify(
                new OfferStatusChangedNotification(
                    $offer,
                    $event->oldStatus,
                    $event->newStatus
                )
            );
        }
    }
}
