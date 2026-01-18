<?php

namespace App\Observers;

use App\Models\Offer;
use App\Models\Status;
use App\Events\OfferStatusChanged;
use App\Events\OfferNeedsReview;

class OfferObserver
{
    /**
     * Handle the Offer "updated" event.
     */
    public function updated(Offer $offer): void
    {
        // Sprawdź czy status się zmienił
        if ($offer->isDirty('status_id')) {
            $oldStatusId = $offer->getOriginal('status_id');
            $newStatusId = $offer->status_id;
            
            // Pobierz statusy
            $oldStatus = Status::find($oldStatusId);
            $newStatus = Status::find($newStatusId);
            
            if (!$oldStatus || !$newStatus) {
                return;
            }
            
            // Handlowiec zmienia na "Do sprawdzenia" → powiadomienie dla WSZYSTKICH regeneracji
            // (nie sprawdzamy changed_by === created_by, bo to powiadomienie idzie do wszystkich regeneracji)
            if ($newStatus->name === 'Do sprawdzenia') {
                event(new OfferNeedsReview($offer));
            } 
            // Regeneracja zmienia na "Wysłana/Zamówienie/Odrzucona" → powiadomienie dla handlowca (created_by)
            // TUTAJ sprawdzamy czy changed_by !== created_by
            elseif (in_array($newStatus->name, ['Wysłana', 'Zamówienie', 'Odrzucona'])) {
                // WAŻNE: Nie wysyłaj powiadomień jeśli ta sama osoba zmienia status
                if ($offer->changed_by === $offer->created_by) {
                    return; // Nie wysyłaj powiadomień
                }
                event(new OfferStatusChanged($offer, $oldStatus, $newStatus));
            }
        }
    }
}
