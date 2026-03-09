<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            // Mesaj sütununun hemen altına 'image' adında boş bırakılabilir (nullable) bir sütun ekliyoruz
            $table->string('image')->nullable()->after('message');
        });
    }

    public function down(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->dropColumn('image');
        });
    }
};
