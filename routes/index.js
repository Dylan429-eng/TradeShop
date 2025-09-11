const express = require("express");
const router = express.Router();

const adminRoutes = require("./admin");
const livreurRoutes = require("./livreur");
const authRoutes = require("./auth");

// Page d’accueil (InterneHomePage)
router.get("/", (req, res) => {
  res.render("InterneHomePage");
});

// Routes admin et livreur
router.use("/admin", adminRoutes);
router.use("/livreur", livreurRoutes);
router.use("/auth", authRoutes);

module.exports = router;
