<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Livraison extends Model
{
    use HasFactory;

    protected $fillable = [
        'statut',
        'date_livraison',
        'user_id',
        'client_id',
        'commande_id'
    ];

    protected $casts = [
        'date_livraison' => 'date',
    ];

    /**
     * Relation avec l'utilisateur (livreur)
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
     * Scope pour les livraisons en attente
     */
    public function scopePending($query)
    {
        return $query->where('statut', 'en attente');
    }

    /**
     * Scope pour les livraisons effectuées
     */
    public function scopeDelivered($query)
    {
        return $query->where('statut', 'livré');
    }
    public function commande()
{
    return $this->belongsTo(Commande::class);
}
}
