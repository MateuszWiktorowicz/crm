<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCoatingPriceIdNullableInOfferDetails extends Migration
{
    public function up()
    {
        Schema::table('offer_details', function (Blueprint $table) {
            // Zmieniamy kolumnę coating_price_id, aby przyjmowała NULL
            $table->integer('coating_price_id')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('offer_details', function (Blueprint $table) {
            // Przywracamy kolumnę coating_price_id do stanu, w którym nie może być NULL
            $table->integer('coating_price_id')->nullable(false)->change();
        });
    }
}
