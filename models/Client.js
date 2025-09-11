// models/Client.js
const { DataTypes, Model } = require("sequelize");

class Client extends Model {
  static associate(models) {
    this.hasMany(models.Commande, { foreignKey: "client_id", as: "commandes" });
    this.hasMany(models.Livraison, { foreignKey: "client_id", as: "livraisons" });
    this.hasMany(models.TransactionPaiement, { foreignKey: "client_id", as: "transactions" });
  }

  getFormattedSolde() {
    return `${Number(this.solde_user).toFixed(2)} FCFA`;
  }
}

module.exports = (sequelize, DataTypes) => {
  Client.init(
    {
      name: DataTypes.STRING,
      email: { type: DataTypes.STRING, unique: true },
      password: DataTypes.STRING,
      telephone: DataTypes.STRING,
      lieu: DataTypes.STRING,
      solde_user: { type: DataTypes.DECIMAL(10, 2), defaultValue: 0.0 },
    },
    {
      sequelize,
      modelName: "Client",
      tableName: "clients",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return Client;
};
