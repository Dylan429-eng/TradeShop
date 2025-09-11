// models/index.js
const { Sequelize, DataTypes } = require("sequelize");
const sequelize = require("../server/db.js"); // instance Sequelize

// Importer les définitions de modèles (fonctions)
const defineUser = require("./User");
const defineClient = require("./Client");
const defineCategorie = require("./Categorie");
const defineProduit = require("./Produit");
const defineCommande = require("./Commande");
const defineCommandeDetail = require("./CommandeDetail");
const defineLivraison = require("./Livraison");
const defineMessage = require("./Message");
const defineTransactionPaiement = require("./TransactionPaiement");
const commandeDetailObserver = require("../observers/commandeDetailObserver");

// Initialiser les modèles en passant (sequelize, DataTypes)
const models = {
  User: defineUser(sequelize, DataTypes),
  Client: defineClient(sequelize, DataTypes),
  Categorie: defineCategorie(sequelize, DataTypes),
  Produit: defineProduit(sequelize, DataTypes),
  Commande: defineCommande(sequelize, DataTypes),
  CommandeDetail: defineCommandeDetail(sequelize, DataTypes),
  Livraison: defineLivraison(sequelize, DataTypes),
  Message: defineMessage(sequelize, DataTypes),
  TransactionPaiement: defineTransactionPaiement(sequelize, DataTypes),
};

// Exécuter les associations
Object.values(models).forEach((model) => {
  if (model.associate) {
    model.associate(models);
  }
});

// Enregistrer les observers
commandeDetailObserver.register({
  CommandeDetail: models.CommandeDetail,
  Produit: models.Produit
});

module.exports = { sequelize, Sequelize, ...models };
