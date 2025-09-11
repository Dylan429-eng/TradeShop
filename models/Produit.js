// models/Produit.js
const { DataTypes, Model, Op } = require("sequelize");

class Produit extends Model {
  static associate(models) {
    this.belongsTo(models.Categorie, { foreignKey: "categorie_id", as: "categorie" });
    this.belongsTo(models.User, { foreignKey: "user_id", as: "user" });
    this.hasMany(models.CommandeDetail, { foreignKey: "produit_id", as: "details" });
    this.belongsToMany(models.Commande, {
      through: models.CommandeDetail,
      foreignKey: "produit_id",
      otherKey: "commande_id",
      as: "commandes",
    });
  }

  static byCategorie(categorieId) {
    return this.findAll({ where: { categorieId } });
  }

  static inStock() {
    return this.findAll({ where: { stock: { [Op.gt]: 0 } } });
  }

  hasStock(quantity) {
    return this.stock >= quantity;
  }

  async reduceStock(quantity) {
    if (this.hasStock(quantity)) {
      this.stock -= quantity;
      await this.save();
      return true;
    }
    return false;
  }

  async increaseStock(quantity) {
    this.stock += quantity;
    await this.save();
  }
}

module.exports = (sequelize, DataTypes) => {
  Produit.init(
    {
      nom: DataTypes.STRING,
      description: DataTypes.TEXT,
      prix: DataTypes.DECIMAL(10, 2),
      stock: DataTypes.INTEGER,
      image: DataTypes.STRING,
      categorie_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    },
    user_id: {
      type: DataTypes.INTEGER,
      allowNull: false
    }
  },
    {
      sequelize,
      modelName: "Produit",
      tableName: "produits",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return Produit;
};
