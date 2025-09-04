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
        Schema::create('commandes', function (Blueprint $table) {
            $table->id()->auto_increment();
            $table->enum('statut',['livré','en attente','annulé'])->default('en attente');
            $table->date('date_cmd')->default(now());
            $table->double('total_prix');
            $table->unsignedBigInteger('client_id');
           
            $table->foreign('client_id')->references('id')->on('clients');
           
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commandes');
    }
};
