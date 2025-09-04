<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('transaction_paiements', function (Blueprint $table) {
            $table->id();
            $table->string('type_transaction');
            $table->string('mode_paiement', 50)->nullable();
            $table->string('statut')->default('pending'); // enum remplacé par string
            $table->date('date_transaction')->default(DB::raw('CURRENT_DATE'));
            $table->decimal('montant_transaction', 10, 2)->default(0);

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('client_id')->nullable()->constrained('clients')->onDelete('set null');
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
