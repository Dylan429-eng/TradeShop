// models/Livraison.js
const { DataTypes, Model } = require("sequelize");

class Livraison extends Model {
  static associate(models) {
    this.belongsTo(models.User, { foreignKey: "user_id", as: "livreur" });
    this.belongsTo(models.Client, { foreignKey: "client_id", as: "client" });
    this.belongsTo(models.Commande, { foreignKey: "commande_id", as: "commande" });
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
  Livraison.init(
    {
      statut: DataTypes.STRING,
      date_livraison: DataTypes.DATE,
    },
    {
      sequelize,
      modelName: "Livraison",
      tableName: "livraisons",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return Livraison;
};
