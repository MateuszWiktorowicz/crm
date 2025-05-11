<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCoatingPriceIdNullableInOfferDetails extends Migration
{
    public function up()
    {
        // Usuwamy klucz obcy przed dokonaniem zmiany kolumny
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropForeign(['coating_price_id']); // Usuwamy klucz obcy
        });

        // Zmieniamy kolumnę coating_price_id na nullable
        Schema::table('offer_details', function (Blueprint $table) {
            $table->integer('coating_price_id')->nullable()->change(); // Ustawiamy nullable
        });

        // Ponownie dodajemy klucz obcy po modyfikacji kolumny
        Schema::table('offer_details', function (Blueprint $table) {
            $table->foreign('coating_price_id')->references('id')->on('coating_prices')->onDelete('set null'); // Klucz obcy
        });
    }

    public function down()
    {
        // Usuwamy klucz obcy przed dokonaniem zmiany
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropForeign(['coating_price_id']);
        });

        // Przywracamy kolumnę coating_price_id do stanu, w którym nie może być NULL
        Schema::table('offer_details', function (Blueprint $table) {
            $table->integer('coating_price_id')->nullable(false)->change(); // Usuwamy nullable
        });

        // Ponownie dodajemy klucz obcy
        Schema::table('offer_details', function (Blueprint $table) {
            $table->foreign('coating_price_id')->references('id')->on('coating_prices')->onDelete('restrict'); // Ustawiamy klucz obcy
        });
    }
}
