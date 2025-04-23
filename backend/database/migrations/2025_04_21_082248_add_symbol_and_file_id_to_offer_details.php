<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSymbolAndFileIdToOfferDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->string('symbol')->nullable(); // Dodanie kolumny symbol
            $table->unsignedBigInteger('file_id')->nullable(); // Dodanie kolumny file_id
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropColumn('symbol');
            $table->dropColumn('file_id');
        });
    }
}
