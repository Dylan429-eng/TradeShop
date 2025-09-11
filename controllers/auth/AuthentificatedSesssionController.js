const bcrypt = require('bcryptjs');
const { User } = require('../../models'); 

exports.showLogin = (req, res) => {
  res.render('auth/login', {
    status: req.flash('status') || null, // ou simplement null si tu n'utilises pas flash
    errors: {},
    old: {} ,   // pas d'erreurs au premier affichage
    title: 'Connexion'
  });
}
  
exports.login = async (req, res) => {
  const { email, password, remember } = req.body;

  try {
    // Vérifier que l'email existe
    const user = await User.findOne({
  where: { email },
  attributes: { include: ["password"] }
});
    if (!user) {
  console.error("❌ Aucun utilisateur trouvé avec cet email:", email);
  req.flash("error", "Email ou mot de passe incorrect");
  req.session.oldInput = { email };
  return res.redirect("/auth/login");
  }

    if (!user.password) {
  console.error("⚠️ Aucun mot de passe trouvé pour cet utilisateur:", user.email);
  req.flash("error", "Email ou mot de passe incorrect");
  req.session.oldInput = { email };
  return res.redirect("/auth/login");
}

    // Vérifier le mot de passe
    const validPassword = await bcrypt.compare(password, user.password);
    if (!validPassword) {
  req.flash("error", "Email ou mot de passe incorrect");
  req.session.oldInput = { email };
  return res.redirect("/auth/login");
}

    // Créer la session
    req.session.userId = user.id;
    req.session.user = {
      id: user.id,
      name: user.name,
      email: user.email,
      role: user.role
    };

    // Si "Remember me", prolonger la durée du cookie
    if (remember) {
      req.session.cookie.maxAge = 30 * 24 * 60 * 60 * 1000; // 30 jours
    } else {
      req.session.cookie.expires = false; // expire à la fermeture du navigateur
    }

    // Rediriger selon le rôle
    if (user.role === "livreur") return res.redirect("/livreur/dashboard");
    if (user.role === "admin") return res.redirect("/admin/dashboard");
    if (user.role === "service_client") return res.redirect("/support/dashboard");

    // Cas par défaut
    res.redirect("/");
  } catch (err) {
    console.error(err);
    req.flash("error", "Erreur serveur");
    res.redirect("/auth/login");
  }
};

// Déconnexion
exports.logout = (req, res) => {
  req.session.destroy(err => {
    if (err) console.error(err);
    res.redirect("/auth/login");
  });
};
exports.me = (req, res) => {
  if (!req.session.userId) {
    return res.status(401).json({ success: false, message: 'Non authentifié' });
  }

  return res.json({
    success: true,
    user: {
      id: req.session.userId,
      role: req.session.role,
    },
  });
};