// models/Message.js
const { DataTypes, Model } = require("sequelize");

class Message extends Model {
  static unread() {
    return this.findAll({ where: { read: false } });
  }

  static read() {
    return this.findAll({ where: { read: true } });
  }

  markAsRead() {
    return this.update({ read: true });
  }
}

module.exports = (sequelize, DataTypes) => {
  Message.init(
    {
      content: DataTypes.TEXT,
      read: { type: DataTypes.BOOLEAN, defaultValue: false },
    },
    {
      sequelize,
      modelName: "Message",
      tableName: "messages",
      underscored: true,  // <-- important : Sequelize mappe createdAt → created_at
    timestamps: true  
    }
  );
  return Message;
};
