<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionPaiement extends Model
{
    use HasFactory;

    protected $fillable = [
        'type_transaction',
        'mode_paiement',
        'statut',
        'date_transaction',
        'montant_transaction',
        'user_id',
        'client_id',
    ];

    protected $casts = [
        'date_transaction' => 'date',
    ];

    /**
     * Relation avec l'utilisateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les transactions réussies
     */
    public function scopeSuccessful($query)
    {
        return $query->where('statut', 'successful');
    }

    /**
     * Scope pour les transactions en attente
     */
    public function scopePending($query)
    {
        return $query->where('statut', 'pending');
    }
}
