<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOfferPdfInfoTable extends Migration
{
    public function up(): void
    {
        Schema::create('offer_pdf_info', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offer_id')->constrained('offers')->onDelete('cascade');
            $table->string('delivery_time')->nullable();
            $table->string('offer_validity')->nullable();
            $table->string('payment_terms')->nullable();
            $table->boolean('display_discount')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('offer_pdf_info');
    }
}

