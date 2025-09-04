<?php

namespace App\Observers;

use App\Models\CommandeDetail;
use App\Models\Produit;

class CommandeDetailObserver
{
    /**
     * Handle the CommandeDetail "created" event.
     */
    public function created(CommandeDetail $commandeDetail): void
    {
        // Décrémenter le stock du produit lors de la création d'un détail de commande
        $produit = $commandeDetail->produit;
        if ($produit && $produit->stock >= $commandeDetail->quantity) {
            $produit->decrement('stock', $commandeDetail->quantity);
        }
    }

    /**
     * Handle the CommandeDetail "updated" event.
     */
    public function updated(CommandeDetail $commandeDetail): void
    {
        // Si la quantité a changé, ajuster le stock
        if ($commandeDetail->isDirty('quantity')) {
            $oldQuantity = $commandeDetail->getOriginal('quantity');
            $newQuantity = $commandeDetail->quantity;
            $difference = $newQuantity - $oldQuantity;
            
            $produit = $commandeDetail->produit;
            if ($produit) {
                if ($difference > 0) {
                    // Quantité augmentée, décrémenter le stock
                    if ($produit->stock >= $difference) {
                        $produit->decrement('stock', $difference);
                    }
                } else {
                    // Quantité diminuée, incrémenter le stock
                    $produit->increment('stock', abs($difference));
                }
            }
        }
    }

    /**
     * Handle the CommandeDetail "deleted" event.
     */
    public function deleted(CommandeDetail $commandeDetail): void
    {
        // Remettre le stock lors de la suppression d'un détail de commande
        $produit = $commandeDetail->produit;
        if ($produit) {
            $produit->increment('stock', $commandeDetail->quantity);
        }
    }
}