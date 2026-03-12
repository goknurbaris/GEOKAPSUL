<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            // image sütunundan hemen sonra unlock_date sütununu ekler
            $table->date('unlock_date')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->dropColumn('unlock_date');
        });
    }
};
