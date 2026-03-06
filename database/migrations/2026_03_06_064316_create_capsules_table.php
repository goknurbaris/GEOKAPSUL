<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
public function up(): void
    {
        Schema::create('capsules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Kapsülü hangi kullanıcı bıraktı?
            $table->text('message'); // Kapsülün içindeki gizli not
            $table->decimal('latitude', 10, 7); // Enlem (Haritadaki konumu)
            $table->decimal('longitude', 10, 7); // Boylam (Haritadaki konumu)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('capsules');
    }
};
