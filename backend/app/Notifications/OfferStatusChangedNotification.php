<?php

namespace App\Notifications;

use App\Models\Offer;
use App\Models\Status;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OfferStatusChangedNotification extends Notification
{
    use Queueable;

    public Offer $offer;
    public Status $oldStatus;
    public Status $newStatus;

    /**
     * Create a new notification instance.
     */
    public function __construct(Offer $offer, Status $oldStatus, Status $newStatus)
    {
        $this->offer = $offer;
        $this->oldStatus = $oldStatus;
        $this->newStatus = $newStatus;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $offerNumber = $this->offer->offer_number ?? '#' . $this->offer->id;
        $customerName = $this->offer->customer ? $this->offer->customer->name : 'Nieznany klient';
        
        return [
            'type' => 'offer_status_changed',
            'title' => 'Oferta zmieniła status',
            'message' => "Oferta {$offerNumber} dla {$customerName} zmieniła status z \"{$this->oldStatus->name}\" na \"{$this->newStatus->name}\"",
            'offer_id' => $this->offer->id,
            'offer_number' => $offerNumber,
            'customer_name' => $customerName,
            'old_status' => $this->oldStatus->name,
            'new_status' => $this->newStatus->name,
        ];
    }
}
