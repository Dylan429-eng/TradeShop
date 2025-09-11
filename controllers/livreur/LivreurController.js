// controllers/livreurController.js
const { Op } = require("sequelize");
const {
  Produit,
  Commande,
  CommandeDetail,
  Categorie,
  Client,
  Livraison,
  Message,
  TransactionPaiement,
  User,
} = require("../../models");

exports.dashboard = async (req, res) => {
  const userId = req.session.user?.id; // ✅ ID du livreur connecté
  if (!userId) {
    req.flash("error", "Vous devez être connecté.");
    return res.redirect("/auth/login");
  }

  // Commandes en cours
  const commandesEnCours = await Commande.findAll({
    where: {
      statut: { [Op.ne]: "livré" },
    },
    include: [
      { model: Client, as: "client" },
      { model: Produit, as: "produits" },
      { model: Livraison, as: "livraison", where: { user_id: userId } },
    ],
  });

  // Commandes livrées aujourd'hui
  const today = new Date();
  const commandesJour = await Commande.findAll({
    where: {
      statut: "livré",
      updated_at: {
        [Op.between]: [
          new Date(today.setHours(0, 0, 0, 0)),
          new Date(today.setHours(23, 59, 59, 999)),
        ],
      },
    },
    include: [
      { model: Client, as: "client" },
      { model: Produit, as: "produits" },
      { model: Livraison, as: "livraison", where: { user_id: userId } },
    ],
  });

  const commandesJourLivrees = commandesJour.length;
  const commandesAssignes = commandesEnCours.length;
  const totalCommandes = await Commande.count({
    include: [{ model: Livraison, as: "livraison", where: { user_id: userId } }],
  });

  res.render("livreur/dashboard", {
    layout: 'layouts/app', 
    user: req.session.user,
    commandesEnCours,
    commandesJour,
    commandesJourLivrees,
    commandesAssignes,
    totalCommandes,
  });
};

exports.confirmerLivraison = async (req, res) => {
  const userId = req.session.user?.id;
  const commandeId = req.params.id;

  const commande = await Commande.findByPk(commandeId, {
    include: [{ model: Livraison, as: "livraison" }],
  });

  if (
    commande &&
    commande.livraison &&
    commande.livraison.user_id.toString() === userId.toString()
  ) {
    await commande.update({ statut: "livré" });
    await commande.livraison.update({ statut: "livré" });

    req.flash("success", "Commande et livraison marquées comme livrées.");
    return res.redirect("/livreur/dashboard");
  }

  req.flash("error", "Vous ne pouvez pas confirmer cette commande.");
  return res.redirect("back");
};
