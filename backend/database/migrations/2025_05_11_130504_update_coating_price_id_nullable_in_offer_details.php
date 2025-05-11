<?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;
    
    class UpdateCoatingPriceIdAsNullableInOfferDetails extends Migration
    {
        public function up()
        {
            Schema::table('offer_details', function (Blueprint $table) {
                // Zmieniamy kolumnę coating_price_id na bigint, jeśli jest to konieczne
                $table->bigInteger('coating_price_id')->nullable()->change();
    
                // Dodajemy klucz obcy
                $table->foreign('coating_price_id')
                      ->references('id')
                      ->on('coating_prices')
                      ->onDelete('set null');
            });
        }
    
        public function down()
        {
            Schema::table('offer_details', function (Blueprint $table) {
                // Usuwamy klucz obcy
                $table->dropForeign(['coating_price_id']);
    
                // Przywracamy oryginalny typ
                $table->integer('coating_price_id')->nullable()->change();
            });
        }
    }
    
