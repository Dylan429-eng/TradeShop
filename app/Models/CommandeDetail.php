<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommandeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'commande_id',
        'produit_id',
        'quantity',
        'prix',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'prix' => 'decimal:2',
    ];

    /**
     * Relation avec la commande
     */
    public function commande()
    {
        return $this->belongsTo(Commande::class);
    }

    /**
     * Relation avec le produit
     */
    public function produit()
    {
        return $this->belongsTo(Produit::class);
    }

    /**
     * Accessor pour le sous-total
     */
    public function getSubtotalAttribute()
    {
        return $this->quantity * $this->prix;
    }

    /**
     * Accessor pour le sous-total formaté
     */
    public function getFormattedSubtotalAttribute()
    {
        return number_format($this->subtotal, 2) . ' FCFA';
    }

    /**
     * Accessor pour le prix unitaire formaté
     */
    public function getFormattedPrixAttribute()
    {
        return number_format($this->prix, 2) . ' FCFA';
    }
}
