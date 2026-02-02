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
            $table->boolean('is_coating_price_manual')->default(false)->after('coating_net_price');
            $table->boolean('is_tool_price_manual')->default(false)->after('tool_net_price');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('offer_details', function (Blueprint $table) {
            $table->dropColumn(['is_coating_price_manual', 'is_tool_price_manual']);
        });
    }
};
