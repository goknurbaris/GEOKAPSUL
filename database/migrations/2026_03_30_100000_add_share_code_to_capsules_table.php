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
        Schema::table('capsules', function (Blueprint $table) {
            $table->string('share_code', 16)->nullable()->unique()->after('pin_code');
            $table->index('share_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->dropIndex(['share_code']);
            $table->dropColumn('share_code');
        });
    }
};
