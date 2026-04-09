<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->unsignedBigInteger('coating_type_id')->nullable()->after('diameter');
            $table->foreign('coating_type_id')->references('id')->on('coating_types')->nullOnDelete();
        });

        // Migrate existing coating_name → coating_type_id
        DB::statement("
            UPDATE tools t
            JOIN coating_types ct ON ct.mastermet_name = t.coating_name
            SET t.coating_type_id = ct.id
            WHERE t.coating_name IS NOT NULL
        ");

        Schema::table('tools', function (Blueprint $table) {
            $table->dropColumn('coating_name');
        });
    }

    public function down(): void
    {
        Schema::table('tools', function (Blueprint $table) {
            $table->string('coating_name')->nullable()->after('diameter');
        });

        DB::statement("
            UPDATE tools t
            JOIN coating_types ct ON ct.id = t.coating_type_id
            SET t.coating_name = ct.mastermet_name
            WHERE t.coating_type_id IS NOT NULL
        ");

        Schema::table('tools', function (Blueprint $table) {
            $table->dropForeign(['coating_type_id']);
            $table->dropColumn('coating_type_id');
        });
    }
};
