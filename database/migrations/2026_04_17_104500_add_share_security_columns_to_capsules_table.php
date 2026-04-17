<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->timestamp('share_expires_at')->nullable()->after('share_code');
            $table->timestamp('share_revoked_at')->nullable()->after('share_expires_at');

            $table->index('share_expires_at');
            $table->index('share_revoked_at');
        });
    }

    public function down(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->dropIndex(['share_expires_at']);
            $table->dropIndex(['share_revoked_at']);
            $table->dropColumn(['share_expires_at', 'share_revoked_at']);
        });
    }
};

