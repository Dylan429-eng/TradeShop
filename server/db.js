// server/db.js
const { Sequelize } = require('sequelize');
require('dotenv').config();

const sequelize = new Sequelize(
  process.env.PG_DB_NAME,
  process.env.PG_USER,
  process.env.PG_PASSWORD,
  {
    host: process.env.PG_HOST,
    port: process.env.PG_PORT || 5432,
    dialect: 'postgres',
    logging: false,
    dialectOptions: process.env.PG_SSL === 'true'
      ? {
          ssl: {
            require: true,
            rejectUnauthorized: false, // Render + Supabase ont des certificats gérés
          },
        }
      : {},
    pool: {
      max: 10,       // nombre max de connexions simultanées
      min: 0,
      acquire: 30000, // timeout d’acquisition
      idle: 10000,   // libère les connexions inactives
    },
  }
);

// Vérification connexion DB
(async () => {
  try {
    await sequelize.authenticate();
    console.log('✅ Database connected successfully');
  } catch (err) {
    console.error('❌ Unable to connect to the database:', err.message);
  }
})();

module.exports = sequelize;
