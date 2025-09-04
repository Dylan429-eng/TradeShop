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
        Schema::create('transaction_paiements', function (Blueprint $table) {
            $table->id()->auto_increment();
            $table->string('type_transaction');
            $table->string('mode_paiement', 50)->nullable();
            $table->enum('statut',['succesful','pending','failed'])->default('pending');
            $table->date('date_transaction')->default(now());
            $table->decimal('montant_transaction')->default(0);

            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('client_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('client_id')->references('id')->on('clients');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_paiements');
    }
};
