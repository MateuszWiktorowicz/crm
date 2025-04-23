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
}
