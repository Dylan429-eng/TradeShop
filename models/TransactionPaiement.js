// models/TransactionPaiement.js
const { DataTypes, Model } = require("sequelize");

class TransactionPaiement extends Model {
  static associate(models) {
    this.belongsTo(models.User, { foreignKey: "user_id", as: "user" });
    this.belongsTo(models.Client, { foreignKey: "client_id", as: "client" });
  }

  static byStatut(statut) {
    return this.findAll({ where: { statut } });
  }

  static successful() {
    return this.findAll({ where: { statut: "successful" } });
  }

  static pending() {
    return this.findAll({ where: { statut: "pending" } });
  }
}

module.exports = (sequelize, DataTypes) => {
  TransactionPaiement.init(
    {
      type_transaction: DataTypes.STRING,
      mode_paiement: DataTypes.STRING,
      statut: DataTypes.STRING,
      date_transaction: DataTypes.DATE,
      montant_transaction: DataTypes.DECIMAL(10, 2),
      user_id: {
      type: DataTypes.INTEGER, // ou BIGINT selon ta table users
    allowNull: false
},
client_id: {
      type: DataTypes.INTEGER,
      allowNull: true
    }
    },
    {
      sequelize,
      modelName: "TransactionPaiement",
      tableName: "transaction_paiements",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return TransactionPaiement;
};
