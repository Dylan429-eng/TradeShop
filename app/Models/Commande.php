<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    use HasFactory;

    protected $fillable = [
        'statut',
        'date_cmd',
        'total_prix',
        'client_id',
    ];

    protected $casts = [
        'date_cmd' => 'date',
        'total_prix' => 'decimal:2',
    ];

    /**
     * Relation avec le client
     */
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Relation avec les détails de commande
     */
    public function commandeDetails()
    {
        return $this->hasMany(CommandeDetail::class);
    }

    /**
     * Relation avec la livraison
     */
    public function livraison()
    {
        return $this->hasOne(Livraison::class,'commande_id');
    }

    /**
     * Accessor pour le prix total formaté
     */
    public function getFormattedTotalPrixAttribute()
    {
        return number_format($this->total_prix, 2) . ' FCFA';
    }

    /**
     * Scope pour filtrer par statut
     */
    public function scopeByStatut($query, $statut)
    {
        return $query->where('statut', $statut);
    }

    /**
     * Scope pour les commandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('statut', 'en attente');
    }

    /**
     * Scope pour les commandes livrées
     */
    public function scopeDelivered($query)
    {
        return $query->where('statut', 'livré');
    }

    /**
     * Calculer le total de la commande
     */
    public function calculateTotal()
    {
        return $this->commandeDetails->sum(function ($detail) {
            return $detail->quantity * $detail->prix;
        });
    }
    public function produits()
    {
    return $this->belongsToMany(\App\Models\Produit::class, 'commande_details');
    }
    public function getLivreurAttribute()
{
    return $this->livraison ? $this->livraison->user : null;
}
}
