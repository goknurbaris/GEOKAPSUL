<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->string('category')->default('memory'); // memory, gift, mystery, game, anniversary
            $table->integer('views')->default(0);
            $table->json('reactions')->nullable(); // {"❤️": 5, "😍": 3}
            $table->boolean('is_anniversary')->default(false);
            $table->foreignId('parent_capsule_id')->nullable()->constrained('capsules')->onDelete('set null'); // Hazine avı zinciri
            $table->integer('chain_order')->nullable(); // Hazine avı sırası
            $table->text('hint')->nullable(); // Sonraki kapsül ipucu
        });
    }

    public function down(): void
    {
        Schema::table('capsules', function (Blueprint $table) {
            $table->dropForeign(['parent_capsule_id']);
            $table->dropColumn(['category', 'views', 'reactions', 'is_anniversary', 'parent_capsule_id', 'chain_order', 'hint']);
        });
    }
};
