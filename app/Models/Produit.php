<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $fillable = [
        'nom',
        'description',
        'prix',
        'stock',
        'image',
        'categorie_id',
        'user_id',
    ];

    protected $casts = [
        'prix' => 'decimal:2',
        'stock' => 'integer',
    ];

    /**
     * Relation avec la catégorie
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class);
    }

    /**
     * Relation avec l'utilisateur créateur
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec les détails de commande
     */
    public function commandeDetails()
    {
        return $this->hasMany(CommandeDetail::class);
    }

    /**
     * Accessor pour le prix formaté
     */
    public function getFormattedPrixAttribute()
    {
        return number_format($this->prix, 2) . ' FCFA';
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategorie($query, $categorieId)
    {
        return $query->where('categorie_id', $categorieId);
    }

    /**
     * Scope pour les produits en stock
     */
    public function scopeInStock($query)
    {
        return $query->where('stock', '>', 0);
    }
    /**
     * Vérifier si le produit a suffisamment de stock
     */
    public function hasStock($quantity)
    {
        return $this->stock >= $quantity;
    }

    /**
     * Réduire le stock du produit
     */
    public function reduceStock($quantity)
    {
        if ($this->hasStock($quantity)) {
            $this->decrement('stock', $quantity);
            return true;
        }
        return false;
    }

    /**
     * Augmenter le stock du produit
     */
    public function increaseStock($quantity)
    {
        $this->increment('stock', $quantity);
    }
}
