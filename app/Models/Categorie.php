<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
    ];

    /**
     * Relation avec les produits
     */
    public function produits()
    {
        return $this->hasMany(Produit::class, 'categorie_id');
    }

    /**
     * Scope pour trier par type
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('type', 'asc');
    }
}