<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("offer_details", function(Blueprint $table) {
            $table->dropColumn('tool_total_net_price');
            $table->dropColumn('tool_total_gross_price');
            $table->dropColumn('coating_quantity');
            $table->dropColumn('coating_discount');
            $table->dropColumn('coating_gross_price');
            $table->renameColumn('tool_discount', 'discount');
            $table->renameColumn('tool_quantity', 'quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("offer_details", function(Blueprint $table) {
            $table->decimal('tool_total_net_price', 10, 2);
            $table->dropColumn('tool_total_gross_price');
            $table->integer('coating_quantity');
            $table->decimal('coating_discount', 5, 2);
            $table->decimal('coating_gross_price', 10, 2);
            $table->renameColumn('discount', 'tool_discount');
            $table->renameColumn('quantity', 'tool_quantity');
        });
    }
};
