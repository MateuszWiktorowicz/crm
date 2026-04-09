<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable()->change();
            $table->string('coating_name')->nullable()->after('diameter');
            $table->decimal('face_grinding_price', 10, 2)->nullable()->after('coating_name');
            $table->decimal('full_grinding_price', 10, 2)->nullable()->after('face_grinding_price');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->decimal('price', 10, 2)->nullable(false)->change();
            $table->dropColumn(['coating_name', 'face_grinding_price', 'full_grinding_price']);
        });
    }
};
