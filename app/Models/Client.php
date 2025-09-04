<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Client extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'telephone',
        'lieu',
        'solde_user',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
        'solde_user' => 'decimal:2',
    ];

    /**
     * Relation avec les commandes
     */
    public function commandes()
    {
        return $this->hasMany(Commande::class);
    }

    /**
     * Relation avec les livraisons
     */
    public function livraisons()
    {
        return $this->hasMany(Livraison::class);
    }

    /**
     * Relation avec les transactions de paiement
     */
    public function transactionPaiements()
    {
        return $this->hasMany(TransactionPaiement::class);
    }

    /**
     * Accessor pour le solde formaté
     */
    public function getFormattedSoldeAttribute()
    {
        return number_format($this->solde_user, 2) . ' FCFA';
    }
}
