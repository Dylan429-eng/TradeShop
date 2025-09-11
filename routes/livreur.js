// routes/livreur.js
const express = require("express");
const router = express.Router();
const livreurController = require("../controllers/livreur/LivreurController");
const { isAuthenticated } = require("../middlewares/auth");

router.use(isAuthenticated);

router.get("/dashboard", livreurController.dashboard);
router.post("/commandes/:id/confirmer", livreurController.confirmerLivraison);

module.exports = router;
