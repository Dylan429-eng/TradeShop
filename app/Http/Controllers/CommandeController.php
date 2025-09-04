<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\CommandeDetail;
use App\Models\Produit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CommandeController extends Controller
{
    /**
     * Créer une nouvelle commande avec vérification de stock
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'produits' => 'required|array',
            'produits.*.produit_id' => 'required|exists:produits,id',
            'produits.*.quantity' => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            // Vérifier le stock disponible pour tous les produits
            foreach ($request->produits as $item) {
                $produit = Produit::findOrFail($item['produit_id']);
                if (!$produit->hasStock($item['quantity'])) {
                    throw new \Exception("Stock insuffisant pour le produit: {$produit->nom}. Stock disponible: {$produit->stock}");
                }
            }

            // Créer la commande
            $commande = Commande::create([
                'client_id' => $request->client_id,
                'statut' => 'en attente',
                'date_cmd' => now(),
                'total_prix' => 0, // Sera calculé après
            ]);

            $totalPrix = 0;

            // Créer les détails de commande (l'observer se chargera de décrémenter le stock)
            foreach ($request->produits as $item) {
                $produit = Produit::findOrFail($item['produit_id']);
                
                $commandeDetail = CommandeDetail::create([
                    'commande_id' => $commande->id,
                    'produit_id' => $item['produit_id'],
                    'quantity' => $item['quantity'],
                    'prix' => $produit->prix,
                ]);

                $totalPrix += $commandeDetail->subtotal;
            }

            // Mettre à jour le total de la commande
            $commande->update(['total_prix' => $totalPrix]);
        });

        return response()->json(['success' => true, 'message' => 'Commande créée avec succès']);
    }

    /**
     * Annuler une commande et remettre le stock
     */
    public function cancel($id)
    {
        DB::transaction(function () use ($id) {
            $commande = Commande::findOrFail($id);
            
            if ($commande->statut !== 'en attente') {
                throw new \Exception('Seules les commandes en attente peuvent être annulées');
            }

            // Supprimer les détails de commande (l'observer remettra le stock)
            $commande->commandeDetails()->delete();
            
            // Supprimer la commande
            $commande->delete();
        });

        return response()->json(['success' => true, 'message' => 'Commande annulée avec succès']);
    }
}