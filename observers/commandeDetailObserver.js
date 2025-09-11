// observers/commandeDetailObserver.js

module.exports = {
register({ CommandeDetail, Produit }) {
    // Lors de la création → décrémenter le stock
    CommandeDetail.addHook('afterCreate', async (commandeDetail) => {
      try {
        const produit = await Produit.findByPk(commandeDetail.produitId);
        if (produit && produit.stock >= commandeDetail.quantity) {
          await produit.decrement('stock', { by: commandeDetail.quantity });
        }
      } catch (err) {
        console.error("Erreur afterCreate commandeDetail:", err);
      }
    });

    // Lors de la mise à jour → ajuster la différence
    CommandeDetail.addHook('afterUpdate', async (commandeDetail) => {
      try {
        if (commandeDetail.changed('quantity')) {
          const oldQuantity = commandeDetail.previous('quantity');
          const newQuantity = commandeDetail.quantity;
          const difference = newQuantity - oldQuantity;

          const produit = await Produit.findByPk(commandeDetail.produitId);
          if (produit) {
            if (difference > 0 && produit.stock >= difference) {
              await produit.decrement('stock', { by: difference });
            } else if (difference < 0) {
              await produit.increment('stock', { by: Math.abs(difference) });
            }
          }
        }
      } catch (err) {
        console.error("Erreur afterUpdate commandeDetail:", err);
      }
    });

    // Lors de la suppression → réajouter le stock
   CommandeDetail.addHook('afterDestroy', async (commandeDetail) => {
      try {
        const produit = await Produit.findByPk(commandeDetail.produitId);
        if (produit) {
          await produit.increment('stock', { by: commandeDetail.quantity });
        }
      } catch (err) {
        console.error("Erreur afterDestroy commandeDetail:", err);
      }
    });
  }
};
