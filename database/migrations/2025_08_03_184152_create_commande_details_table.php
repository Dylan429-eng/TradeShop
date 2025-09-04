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
        Schema::create('commande_details', function (Blueprint $table) {
            $table->id()->auto_increment();
            $table->unsignedBigInteger('commande_id');
            $table->unsignedBigInteger('produit_id');
            $table->double('quantity');
            $table->double('prix');
             $table->foreign('commande_id')->references('id')->on('commandes')
            ->onDelete('cascade');
             $table->foreign('produit_id')->references('id')->on('produits')
            ->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commande_details');
    }
};
