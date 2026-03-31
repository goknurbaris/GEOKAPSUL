<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('xp')->default(0);
            $table->integer('level')->default(1);
            $table->integer('capsules_opened')->default(0);
            $table->integer('capsules_created')->default(0);
            $table->decimal('total_distance_km', 10, 2)->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp', 'level', 'capsules_opened', 'capsules_created', 'total_distance_km']);
        });
    }
};
