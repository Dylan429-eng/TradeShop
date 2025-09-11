// server/db.js
const { Sequelize } = require('sequelize');
require('dotenv').config();

const sequelize = new Sequelize(process.env.DATABASE_URL, {
  dialect: 'postgres',
  logging: false,
  dialectOptions: {
    ssl: {
      require: true,
      rejectUnauthorized: false, // Render + Supabase gèrent les certificats
    },
  },
  pool: {
    max: 10,       // nombre max de connexions simultanées
    min: 0,
    acquire: 30000, // timeout d’acquisition
    idle: 10000,   // libère les connexions inactives
  },
});

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
