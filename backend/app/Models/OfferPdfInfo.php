<?php 
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OfferPdfInfo extends Model
{
    protected $table = 'offer_pdf_info';

    protected $fillable = [
        'offer_id',
        'delivery_time',
        'offer_validity',
        'payment_terms',
        'display_discount',
    ];

    public function offer()
    {
        return $this->belongsTo(Offer::class);
    }

    public function toArray()
    {
        return [
            'id' => $this->id,
            'offerId' => $this->offer_id,
            'deliveryTime' => $this->delivery_time,
            'offerValidity' => $this->offer_validity,
            'paymentTerms' => $this->payment_terms,
            'displayDiscount' => $this->display_discount,
        ];
    }
}
