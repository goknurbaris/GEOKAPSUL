<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Rozet tanımları
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique(); // first-capsule, explorer-10, etc.
            $table->string('name'); // İlk Kapsül, Kaşif, vb.
            $table->text('description');
            $table->string('icon'); // emoji veya icon class
            $table->string('color')->default('indigo'); // tailwind color
            $table->integer('xp_reward')->default(0);
            $table->json('criteria'); // {"type": "capsule_count", "value": 1}
            $table->timestamps();
        });

        // Kullanıcı rozetleri (pivot)
        Schema::create('user_badges', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('badge_id')->constrained()->onDelete('cascade');
            $table->timestamp('earned_at');
            $table->unique(['user_id', 'badge_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_badges');
        Schema::dropIfExists('badges');
    }
};
