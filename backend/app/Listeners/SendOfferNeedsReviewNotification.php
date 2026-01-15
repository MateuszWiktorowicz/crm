<?php

namespace App\Listeners;

use App\Models\User;
use App\Events\OfferNeedsReview;
use App\Notifications\OfferNeedsReviewNotification;

class SendOfferNeedsReviewNotification
{
    /**
     * Handle the event.
     */
    public function handle(OfferNeedsReview $event): void
    {
        $offer = $event->offer;
        
        // Wysyłamy powiadomienie do WSZYSTKICH użytkowników z rolą regeneration
        // Pobieramy wszystkich użytkowników i filtrujemy po roli
        $allUsers = User::all();
        
        foreach ($allUsers as $user) {
            if ($user->hasRole('regeneration')) {
                $user->notify(
                    new OfferNeedsReviewNotification($offer)
                );
            }
        }
    }
}
