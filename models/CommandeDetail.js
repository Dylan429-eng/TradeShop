// models/CommandeDetail.js
const { DataTypes, Model } = require("sequelize");

class CommandeDetail extends Model {
  static associate(models) {
    this.belongsTo(models.Commande, { foreignKey: "commande_id", as: "commande" });
    this.belongsTo(models.Produit, { foreignKey: "produit_id", as: "produit" });
  }

  getSubtotal() {
    return this.quantity * this.prix;
  }

  getFormattedSubtotal() {
    return `${this.getSubtotal().toFixed(2)} FCFA`;
  }

  getFormattedPrix() {
    return `${Number(this.prix).toFixed(2)} FCFA`;
  }
}

module.exports = (sequelize, DataTypes) => {
  CommandeDetail.init(
    {
      quantity: DataTypes.INTEGER,
      prix: DataTypes.DECIMAL(10, 2),
    },
    {
      sequelize,
      modelName: "CommandeDetail",
      tableName: "commande_details",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return CommandeDetail;
};
