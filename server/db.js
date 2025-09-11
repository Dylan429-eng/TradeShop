// server/db.js
const { Sequelize } = require('sequelize');
require('dotenv').config();

const sequelize = new Sequelize(
  process.env.PG_DB_NAME,
  process.env.PG_USER,
  process.env.PG_PASSWORD,
  {
    host: process.env.PG_HOST,
    port: process.env.PG_PORT,
    dialect: 'postgres',
    ssl: process.env.PG_SSL === 'true',
    logging: false, // facultatif, pour désactiver les logs SQL
  }
);

module.exports = sequelize;
