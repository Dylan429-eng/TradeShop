// models/Commande.js
const { DataTypes, Model } = require("sequelize");

class Commande extends Model {
  static associate(models) {
    this.belongsTo(models.Client, { foreignKey: "client_id", as: "client" });
    this.hasMany(models.CommandeDetail, { foreignKey: "commande_id", as: "details" });
    this.hasOne(models.Livraison, { foreignKey: "commande_id", as: "livraison" });
    this.belongsToMany(models.Produit, {
      through: models.CommandeDetail,
      foreignKey: "commande_id",
      otherKey: "produitId",
      as: "produits",
    });
  }

  static byStatut(statut) {
    return this.findAll({ where: { statut } });
  }

  static pending() {
    return this.findAll({ where: { statut: "en attente" } });
  }

  static delivered() {
    return this.findAll({ where: { statut: "livré" } });
  }
}

module.exports = (sequelize, DataTypes) => {
  Commande.init(
    {
      statut: DataTypes.STRING,
      date_cmd: DataTypes.DATE,
      total_prix: DataTypes.DECIMAL(10, 2),
    },
    {
      sequelize,
      modelName: "Commande",
      tableName: "commandes",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return Commande;
};
