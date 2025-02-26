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
        Schema::table('offer_details', function (Blueprint $table) {
            $table->renameColumn('tool_net_price', 'tool_total_net_price');
            $table->renameColumn('tool_gross_price', 'tool_total_gross_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->renameColumn('tool_total_net_price', 'tool_net_price');
            $table->renameColumn('tool_total_gross_price', 'tool_gross_price');
        });
    }
};
