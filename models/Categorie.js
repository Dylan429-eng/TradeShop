// models/Categorie.js
const { DataTypes, Model } = require("sequelize");

class Categorie extends Model {
  static associate(models) {
    this.hasMany(models.Produit, { foreignKey: "categorie_id", as: "produits" });
  }

  static ordered() {
    return this.findAll({ order: [["type", "ASC"]] });
  }
}

module.exports = (sequelize, DataTypes) => {
  Categorie.init(
    {
      type: {
        type: DataTypes.STRING,
        allowNull: false,
      },
    },
    {
      sequelize,
      modelName: "Categorie",
      tableName: "categories",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return Categorie;
};
