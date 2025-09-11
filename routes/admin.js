// routes/admin.js
const express = require("express");
const router = express.Router();
const ecommerceAdmin = require("../controllers/admin/ecommerceAdmin");

const upload = require('../middlewares/uploads');
// Middleware d'auth (à écrire : ex. vérifier si user connecté)
const { isAuthenticated } = require("../middlewares/auth");

// Toutes les routes sont sous /admin avec middleware auth
router.use(isAuthenticated); 
// Dashboard
router.get("/dashboard", ecommerceAdmin.dashboard);

// Produits
router.get("/produits", ecommerceAdmin.produits);
router.get("/produits/create", ecommerceAdmin.createProduit);
router.post("/produitStore",upload.single("image"), ecommerceAdmin.storeProduit);
router.post("/categories/store", ecommerceAdmin.storeCategorie);
router.get("/produits/:id/edit", ecommerceAdmin.editProduit);
router.post("/produitUpdate/:id", upload.single("image"), ecommerceAdmin.updateProduit);
router.delete("/produits/:id", ecommerceAdmin.deleteProduit);

// Commandes
router.get("/commandes", ecommerceAdmin.commandes);
router.get("/commandes/:id", ecommerceAdmin.showCommande);
router.post("/commandes/:id/assigner", ecommerceAdmin.assignerLivreur);
router.post("/commandes/retrait", isAuthenticated, ecommerceAdmin.retrait);

// Clients / Users
router.get("/clients", ecommerceAdmin.clients);
router.get("/users", ecommerceAdmin.users);
// ⚠️ deleteUser doit être implémentée dans ecommerceAdminController
router.delete("/users/:id/delete", ecommerceAdmin.deleteUser);

// Catégories / Livraisons / Transactions / Messages
// router.get("/categories", ecommerceAdmin.categories);
// router.get("/livraisons", ecommerceAdmin.livraisons);
// router.get("/transactions", ecommerceAdmin.transactions);
// router.get("/messages", ecommerceAdmin.messages);

// Statistiques
router.get("/statistiques", ecommerceAdmin.statistiques);

module.exports = router;
