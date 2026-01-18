<?php

namespace App\Notifications;

use App\Models\Offer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class OfferNeedsReviewNotification extends Notification
{
    use Queueable;

    public Offer $offer;

    /**
     * Create a new notification instance.
     */
    public function __construct(Offer $offer)
    {
        $this->offer = $offer;
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
        $salespersonName = $this->offer->createdBy ? $this->offer->createdBy->name : 'Nieznany handlowiec';
        
        return [
            'type' => 'offer_needs_review',
            'title' => 'Oferta wymaga sprawdzenia',
            'message' => "Oferta {$offerNumber} dla {$customerName} (handlowiec: {$salespersonName}) wymaga sprawdzenia",
            'offer_id' => $this->offer->id,
            'offer_number' => $offerNumber,
            'customer_name' => $customerName,
            'salesperson_name' => $salespersonName,
        ];
    }
}
