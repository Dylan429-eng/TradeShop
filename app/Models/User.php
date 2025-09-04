<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'telephone'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    public function produits()
    {
        return $this->hasMany(Produit::class);
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
     * Scope pour filtrer par rôle
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Vérifier si l'utilisateur est admin
     */
    public function isAdmin()
    {
        return in_array($this->role, ['admin', 'super_admin']);
    }

    /**
     * Vérifier si l'utilisateur est vendeur
     */
    public function isVendeur()
    {
        return $this->role === 'vendeur';
    }

    /**
     * Vérifier si l'utilisateur est livreur
     */
    public function isLivreur()
    {
        return $this->role === 'livreur';
    }

}
