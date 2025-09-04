<?php

namespace App\Http\Controllers\Livreur;

use App\Http\Controllers\Controller;
use App\Models\Categorie;
use App\Models\Client;
use App\Models\Commande;
use App\Models\Livraison;
use App\Models\Message;
use App\Models\Produit;
use App\Models\TransactionPaiement;
use App\Models\User;
use Illuminate\Http\Request;

class LivreurController extends Controller
{
public function dashboard()
{
    $userId = auth()->id();

    $commandesEnCours = Commande::whereHas('livraison', function($q) use ($userId) {
        $q->where('user_id', $userId);
    })
    ->where('statut', '!=', 'livré')
    ->with(['client', 'produits', 'livraison'])
    ->get();

    $commandesJour = Commande::whereHas('livraison', function($q) use ($userId) {
        $q->where('user_id', $userId);
    })
    ->where('statut', 'livré')
    ->whereDate('updated_at', now())
    ->with(['client', 'produits', 'livraison'])
    ->get();

    $commandesJourLivrees = $commandesJour->count();
    $commandesAssignes = $commandesEnCours->count();
    $totalCommandes = Commande::whereHas('livraison', function($q) use ($userId) {
        $q->where('user_id', $userId);
    })->count();

    return view('livreur.dashboard', compact(
        'commandesEnCours',
        'commandesJour',
        'commandesJourLivrees',
        'commandesAssignes',
        'totalCommandes'
    ));
}
    public function confirmerLivraison(Commande $commande)
{
    $userId = auth()->id();

    // Vérifier que la commande appartient bien au livreur connecté
    if ($commande->livraison && $commande->livraison->user_id === $userId) {
        // Mettre à jour le statut de la commande
        $commande->statut = 'livré';
        $commande->save();

        // Mettre à jour le statut de la livraison associée
        $commande->livraison->statut = 'livré';
        
        $commande->livraison->save();

        return redirect()->back()->with('success', 'Commande et livraison marquées comme livrées.');
    }

    return redirect()->back()->with('error', 'Vous ne pouvez pas confirmer cette commande.');
}

}
