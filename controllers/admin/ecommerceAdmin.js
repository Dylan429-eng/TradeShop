// controllers/admin/ecommerceAdminController.js
const { Op, fn, col, literal, Sequelize } = require("sequelize");
const {
  sequelize,
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
const axios = require("axios");
const { v4: uuidv4 } = require("uuid");
const path = require('path');
const fs = require('fs');
const supabase = require("../../utils/supabaseClient");
// Dashboard principal admin
exports.dashboard = async (req, res) => {
  try {
    const totalProduits = await Produit.count();
    const totalCommandes = await Commande.count();
    const totalClients = await Client.count();
    const totalUsers = await User.count();

    const commandesEnAttente = await Commande.count({ where: { statut: "en attente" } });
    const livraisonsEnAttente = await Livraison.count({ where: { statut: "en attente" } });
    const messagesNonLus = await Message.count({ where: { read: false } });

    const ventesTotales = await Commande.sum("total_prix") || 0;

    // Ventes aujourd'hui
    const today = new Date().toISOString().split("T")[0];
    const ventesAujourdhui = await Commande.sum("total_prix", {
      where: sequelize.where(sequelize.fn("DATE", col("date_cmd")), today)
    }) || 0;

    // Commandes récentes avec alias corrects
    const commandesRecentes = await Commande.findAll({
  include: [
    { model: Client, as: "client" }, 
    { model: CommandeDetail, as: "details", include: [{ model: Produit, as: "produit" }] }
  ],
  order: [["createdAt", "DESC"]],
  limit: 5
});
    // Produits populaires
  const produitsPopulaires = await Produit.findAll({
  attributes: [
    "id",
    "nom",
    "description",
    "prix",
    "stock",
    "image",
    [fn("COUNT", col("details.id")), "ventes"]
  ],
  include: [
    {
      model: CommandeDetail,
      as: "details",
      attributes: [],
      required: false, // LEFT JOIN pour inclure produits sans ventes
      duplicating: false
    }
  ],
  group: ["Produit.id"],
  order: [[literal("ventes"), "DESC"]],
  limit: 5,
  subQuery: false // empêche Sequelize de créer une sous-requête imbriquée
});


    res.render("admin/dashboard", {
      layout: 'layouts/app', 
      user: req.user,
      header: null, 
       scripts: null,
      totalProduits,
      totalCommandes,
      totalClients,
      totalUsers,
      commandesEnAttente,
      livraisonsEnAttente,
      messagesNonLus,
      ventesTotales,
      ventesAujourdhui,
      commandesRecentes,
      produitsPopulaires
    });

  } catch (err) {
    console.error(err);
    res.status(500).send("Erreur serveur");
  }
};
// Catégories
exports.storeCategorie = async (req, res) => {
  try {
    await Categorie.create({ type: req.body.type });
    res.redirect("/admin/produits?cat_success=Catégorie ajoutée avec succès");
  } catch (err) {
    console.error(err);
    res.status(400).send("Erreur lors de l'ajout de catégorie");
  }
};

// Produits
exports.produits = async (req, res) => {
  try {
    const search = req.query.search || '';
    const categorie = req.query.categorie || '';
    const page = parseInt(req.query.page) || 1;
    const limit = 10;
    const offset = (page - 1) * limit;

    // Construire la condition WHERE
    const where = {};
    if (search) {
      where.nom = { [Op.like]: `%${search}%` };
    }
    if (categorie) {
      where.categorie_id = categorie;
    }

    // Récupérer les produits avec pagination et relations
    const { rows: produits, count } = await Produit.findAndCountAll({
      where,
      include: [
        { model: Categorie, as: 'categorie' },
        { model: User, as: 'user' }
      ],
      limit,
      offset,
      order: [['createdAt', 'DESC']]
    });

    // Générer le HTML de pagination simple
    const totalPages = Math.ceil(count / limit);
    let pagination = '';
    for (let i = 1; i <= totalPages; i++) {
      pagination += `<a href="?page=${i}${search ? `&search=${encodeURIComponent(search)}` : ''}${categorie ? `&categorie=${categorie}` : ''}" class="${i === page ? 'active' : ''}">${i}</a>`;
    }

    // Messages de succès depuis query params
    const success = req.query.success || '';
    const cat_success = req.query.cat_success || '';

    // Rendu de la vue
    res.render("admin/produits/index", {
      produits,
      categories: await Categorie.findAll(),
      search,
      categorie,
      success,
      cat_success,
      errors: {},
      pagination
    });

  } catch (err) {
    console.error(err);
    res.status(500).send("Erreur serveur");
  }
};

exports.createProduit = async (req, res) => {
  const categories = await Categorie.findAll();
  res.render("admin/produits/create",
     { 
      categories ,
      old: {},       // valeur par défaut vide
    errors: {} ,
    csrfToken: req.csrfToken()

  });
};

exports.storeProduit = async (req, res) => {
  try {
    const data = req.body;
    const userId = req.session.user?.id;

    if (!userId) {
      return res.status(401).send("Utilisateur non authentifié");
    }

    let imageUrl = null;

    if (req.file) {
      const fileName = Date.now() + "-" + req.file.originalname.replace(/\s+/g, "_");

      // 🔹 upload du buffer vers Supabase
      const { error } = await supabase.storage
        .from(process.env.SUPABASE_BUCKET)
        .upload(fileName, req.file.buffer, {
          contentType: req.file.mimetype,
        });

      if (error) throw error;

      const { data: publicUrlData } = supabase.storage
        .from(process.env.SUPABASE_BUCKET)
        .getPublicUrl(fileName);

      imageUrl = publicUrlData.publicUrl;
    }

    await Produit.create({
      ...data,
      image: imageUrl,
      user_id: userId,
    });

    res.redirect("/admin/produits?success=Produit ajouté");
  } catch (err) {
    console.error("Erreur storeProduit:", err);
    res.status(400).send("Erreur ajout produit");
  }
};


exports.editProduit = async (req, res) => {
  const produit = await Produit.findByPk(req.params.id);
  const categories = await Categorie.findAll();
  res.render("admin/produits/edit", 
    {
     produit,
     old: {},       // valeur par défaut vide
    errors: {} ,
    csrfToken: req.csrfToken(),
      categories 
    });
};

exports.updateProduit = async (req, res) => {
  try {
    const produit = await Produit.findByPk(req.params.id);
    let data = req.body;

    if (req.file) {
      data.image =  req.file.filename;
    }

    await produit.update(data);
    res.redirect("/admin/produits?success=Produit modifié");
  } catch (err) {
    res.status(400).send("Erreur modification produit");
  }
};

exports.deleteProduit = async (req, res) => {
  const produit = await Produit.findByPk(req.params.id);
  if (!produit) return res.status(404).send("Produit introuvable");

  await produit.destroy();
  res.redirect("/admin/produits?success=Produit supprimé");
};

// Commandes
exports.commandes = async (req, res) => {
  const commandes = await Commande.findAll({
  include: [
    { model: Client, as: "client" },
    { model: CommandeDetail, as: "details", include: [{ model: Produit, as: "produit" }] },
    { model: Livraison, as: "livraison", include: [{ model: User, as: "livreur" }] }
  ],
  order: [["createdAt", "DESC"]]
});
  const montantTotal = await Commande.sum("total_prix") || 0;
  const livreurs = await User.findAll({ where: { role: "livreur" } });
  const success = req.query.success || null;
  const error = req.query.error || null;
  res.render("admin/commandes/index", { commandes, montantTotal, livreurs,success,error  });
};

exports.showCommande = async (req, res) => {
  const id = req.params.id;

  try {
    const commande = await Commande.findByPk(id, {
  include: [
    { model: Client, as: 'client' },
    { 
      model: CommandeDetail, 
      as: 'details',
      include: [{ model: Produit, as: 'produit' }]
    },
    { 
      model: Livraison, 
      as: 'livraison',
      include: [{ model: User, as: 'livreur' }]
    }
  ]
});
    if (!commande) {
      return res.status(404).send('Commande introuvable');
    }

    return res.render('admin/commandes/show', { commande });
    
  } catch (err) {
    console.error(err);
    return res.status(500).send('Erreur serveur');
  }
};


exports.assignerLivreur = async (req, res) => {
  try {
    const { livreur_id } = req.body;
    const commande = await Commande.findByPk(req.params.id, { include: [{ model: Client, as: 'client' }] });
    if (!commande) return res.status(404).send("Commande introuvable");
    if (!livreur_id) return res.status(400).send("Livreur non sélectionné");
    if (!commande.client_id) return res.status(400).send("Client de la commande manquant");

    let livraison = await Livraison.findOne({ where: { commande_id: commande.id } });
    if (!livraison) {
      livraison = await Livraison.create({
        commande_id: commande.id,
        user_id: livreur_id,
        client_id: commande.client_id,
        statut: "en attente"
      });
    } else {
      await livraison.update({ user_id: livreur_id });
    }

    // Envoi de l'email
    const livreur = await User.findByPk(livreur_id);
    if (livreur && livreur.email) {
      const sendCommandeAssigneeEmail = require('../../utils/sendCommandeAssigneeEmail');
      await sendCommandeAssigneeEmail(livreur, commande);
    }

    res.redirect("/admin/commandes?success=Livreur assigné avec succès");
  } catch (err) {
    console.error("Erreur assignerLivreur:", err);
    res.status(500).send("Erreur serveur lors de l'assignation du livreur");
  }
};


// Retrait Campay
exports.retrait = async (req, res) => {
  const { phone_number, amount } = req.body;
  const token = process.env.CAMPAY_TOKEN;

  const brut = parseFloat(amount);
  const frais = brut * 0.05;
  const net = Math.floor(brut - frais);

  try {
    const withdrawResp = await axios.post(
      "https://demo.campay.net/api/withdraw/",
      {
        amount: net,
        to: phone_number,
        description: "Retrait plateforme",
        external_reference: uuidv4()
      },
      { headers: { Authorization: `Token ${token}` } }
    );
    if (net <= 0) {
  return res.redirect(req.get("referer") + "?error=Solde insuffisant pour effectuer le retrait");
}

    const reference = withdrawResp.data.reference;
    if (!reference) return res.redirect(req.get("referer") + "?error=Erreur Retrait" );

    const txResp = await axios.get(`https://demo.campay.net/api/transaction/${reference}/`, {
      headers: { Authorization: `Token ${token}` }
    });

    const { status, operator } = txResp.data;
    
    await TransactionPaiement.create({
      type_transaction: "retrait",
      mode_paiement: operator || "mobile money",
      statut: status,
      date_transaction: new Date(),
      montant_transaction: net,
      user_id: req.user.id
    });

    res.redirect("/admin/commandes?success=Retrait effectué avec succès");
  } catch (err) {
    console.error("Erreur API Campay :", err.response?.data || err.message);
     res.redirect("/admin/commandes?error=Erreur API Campay" );
  }
};

// Statistiques produits
exports.statistiques = async (req, res) => {
  try {
    const periode = req.query.periode || "mois_actuel";
    const now = new Date();
    let start, end;

    switch (periode) {
      case "mois_precedent":
        start = new Date(now.getFullYear(), now.getMonth() - 1, 1);
        end = new Date(now.getFullYear(), now.getMonth(), 0);
        break;
      default:
        start = new Date(now.getFullYear(), now.getMonth(), 1);
        end = new Date(now.getFullYear(), now.getMonth() + 1, 0);
    }

    const produitsStats = await Produit.findAll({
      attributes: [
        "id",
        "nom",
        [
          fn(
            "SUM",
            literal(`
              CASE 
                WHEN "details->commande"."date_cmd" BETWEEN '${start.toISOString()}' AND '${end.toISOString()}'
                THEN details.quantity 
                ELSE 0 
              END
            `)
          ),
          "ventes",
        ],
      ],
      include: [
        {
          model: CommandeDetail,
          as: "details",
          attributes: [],
          include: [{ model: Commande, as: "commande", attributes: [] }],
        },
      ],
      group: ["Produit.id"],
      raw: true,
    });

    // Trier les stats
    const plusVendus = [...produitsStats]
      .sort((a, b) => b.ventes - a.ventes)
      .slice(0, 5);

    const moinsVendus = [...produitsStats]
      .sort((a, b) => a.ventes - b.ventes)
      .slice(0, 5);

    res.render("admin/statistiques/index", {
      plusVendus,
      moinsVendus,
      produitsStats,
      periode,
    });
  } catch (error) {
    console.error("Erreur statistiques :", error);
    res.status(500).send("Erreur serveur");
  }
};

// controllers/admin/ecommerceAdminController.js


// Afficher la liste des clients avec le nombre de commandes
exports.clients = async (req, res) => {
  try {
    // Page courante (par défaut 1)
    const page = parseInt(req.query.page) || 1;
    const limit = 10; // 🔹 nombre d'éléments par page
    const offset = (page - 1) * limit;

    // Nombre total de clients (pour pagination)
    const totalClients = await Client.count();

    // Récupération des clients paginés
    const clients = await Client.findAll({
      include: [
        {
          model: Commande,
          as: "commandes",
          attributes: [],
        },
      ],
      attributes: {
        include: [[fn("COUNT", col("commandes.id")), "commandesCount"]],
      },
      group: ["Client.id"],
      order: [["createdAt", "DESC"]],
      limit,
      offset,
      subQuery: false, // important pour éviter un bug Sequelize avec GROUP BY
    });

    // Formatage des données
    const formattedClients = clients.map((c) => ({
      id: c.id,
      name: c.name,
      email: c.email,
      telephone: c.telephone,
      lieu: c.lieu,
      commandes_count: c.get("commandesCount") || 0,
      created_at_formatted: c.createdAt
        ? c.createdAt.toLocaleDateString("fr-FR")
        : "-",
    }));

    // Calcul du nombre total de pages
    const totalPages = Math.ceil(totalClients / limit);

    // Génération du HTML de pagination (simple)
    let pagination = "";
    for (let i = 1; i <= totalPages; i++) {
      if (i === page) {
        pagination += `<span class="active">${i}</span>`;
      } else {
        pagination += `<a href="?page=${i}">${i}</a>`;
      }
    }

    // Rendu de la vue
    res.render("admin/clients/index", {
      clients: formattedClients,
      pagination,
    });
  } catch (error) {
    console.error("Erreur lors de la récupération des clients:", error);
    req.flash("error", "Impossible de charger la liste des clients.");
    res.redirect("/admin/dashboard");
  }
};
// Afficher la liste des utilisateurs (vendeurs, livreurs, etc.)
exports.users = async (req, res) => {
  try {
    // Récupération de la page courante depuis la query string
    const page = parseInt(req.query.page) || 1;
    const limit = 10; // 🔹 nombre d'utilisateurs par page
    const offset = (page - 1) * limit;

    // Nombre total d'utilisateurs (hors admins)
    const totalUsers = await User.count({
      where: { role: { [Op.ne]: "admin" } }
    });

    // Récupération des utilisateurs paginés
    const users = await User.findAll({
      where: { role: { [Op.ne]: "admin" } },
      order: [["createdAt", "DESC"]],
      limit,
      offset
    });

    // Calcul du nombre total de pages
    const totalPages = Math.ceil(totalUsers / limit);

    // Génération du HTML de pagination
    let pagination = "";
    for (let i = 1; i <= totalPages; i++) {
      if (i === page) {
        pagination += `<span class="active">${i}</span>`;
      } else {
        pagination += `<a href="?page=${i}">${i}</a>`;
      }
    }
    const success = req.query.success || null;
  const error = req.query.error || null;
    // Rendu de la vue
    res.render("admin/employes/index", {
      users,
      pagination,
      success,
      error
    });
  } catch (err) {
    console.error("Erreur lors de la récupération des employés:", err);
    res.status(500).send("Erreur serveur");
  }
};
// Supprimer un utilisateur
exports.deleteUser = async (req, res) => {
  try {
    const user = await User.findByPk(req.params.id);
    if (!user) return res.status(404).send("Utilisateur introuvable");

    await user.destroy();
    res.redirect("/admin/users?success=Utilisateur supprimé");
  } catch (err) {
    console.error(err);
    res.status(500).send("Erreur serveur");
  }
};
