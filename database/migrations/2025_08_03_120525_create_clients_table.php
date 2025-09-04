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
        Schema::create('clients', function (Blueprint $table) {
            $table->id(); // PostgreSQL gère déjà l'auto-increment
            $table->string('name');
            $table->string('email')->unique(); // pas besoin de (191)
            $table->string('password');
            $table->string('telephone')->nullable();
            $table->string('lieu');
            $table->decimal('solde_user', 10, 2)->default(0); // adapté pour stocker un solde numérique
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clients');
    }
};
