<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            // Şifre alanı zorunlu değil (nullable) ve tarih alanından hemen sonra gelsin
            $table->string('pin_code', 4)->nullable()->after('unlock_date');
        });
    }

    public function down(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->dropColumn('pin_code');
        });
    }
};
