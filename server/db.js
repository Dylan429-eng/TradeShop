// server/db.js
const sequelize = new Sequelize(
  process.env.PG_DB_NAME,
  process.env.PG_USER,
  process.env.PG_PASSWORD,
  {
    host: process.env.PG_HOST,
    port: process.env.PG_PORT,
    dialect: 'postgres',
    dialectOptions: {
      ssl: process.env.PG_SSL === 'true' ? { rejectUnauthorized: false } : false,
    },
    logging: false,
  }
);

module.exports = sequelize;
