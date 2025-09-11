// models/User.js
const { DataTypes, Model } = require("sequelize");

class User extends Model {
  isAdmin() {
    return ["admin", "super_admin"].includes(this.role);
  }
  isVendeur() {
    return this.role === "vendeur";
  }
  isLivreur() {
    return this.role === "livreur";
  }

  static byRole(role) {
    return this.findAll({ where: { role } });
  }

  static associate(models) {
    this.hasMany(models.Produit, { foreignKey: "userId", as: "produits" });
    this.hasMany(models.Livraison, { foreignKey: "userId", as: "livraisons" });
    this.hasMany(models.TransactionPaiement, { foreignKey: "userId", as: "transactionPaiements" });
  }
}

module.exports = (sequelize, DataTypes) => {
  User.init(
    {
      name: { type: DataTypes.STRING, allowNull: false },
      email: { type: DataTypes.STRING, unique: true, allowNull: false },
      password: { type: DataTypes.STRING, allowNull: false },
      role: { type: DataTypes.STRING, defaultValue: "user" },
      telephone: { type: DataTypes.STRING },
      email_verified_at: { type: DataTypes.DATE },
      remember_token: { type: DataTypes.STRING },
    },
    {
      sequelize,
      modelName: "User",
      tableName: "users",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
      timestamps: true  ,
      defaultScope: {
        attributes: { exclude: ["password", "remember_token"] },
      },
    }
  );
  return User;
};
