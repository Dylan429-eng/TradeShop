const pool = require('../../server/db');
const bcrypt = require('bcryptjs');
const { User } = require('../../models'); 
exports.showForm = (req, res) => {
  res.render('auth/register', {
    old: {},        // pas de données précédentes au premier affichage
    errors: {},     // pas d'erreurs au premier affichage
    title: 'Inscription'
  });
};
// exports.store = async (req, res) => {
//   const { name, email, password, password_confirmation, role, telephone } = req.body;

//   if (!name || !email || !password || password !== password_confirmation)
//     return res.status(422).json({ error: 'Validation échouée' });

//   try {
//     const existing = await pool.query(`SELECT * FROM users WHERE email=$1`, [email]);
//     if (existing.rows.length) return res.status(422).json({ error: 'Email déjà utilisé' });

//     const hashed = await bcrypt.hash(password, 12);
//     const result = await pool.query(
//       `INSERT INTO users (name,email,password,role,telephone) VALUES ($1,$2,$3,$4,$5) RETURNING *`,
//       [name, email, hashed, role, telephone]
//     );
//     const user = result.rows[0];

//     req.session.userId = user.id;
//     req.session.user = user;

//     res.json({ redirect: '/verification-notice' });
//   } catch (err) {
//     console.error(err);
//     res.status(500).json({ error: 'Erreur serveur' });
//   }
// };
exports.store = async (req, res) => {
  const { name, email, password, password_confirmation, role, telephone } = req.body;

  // Vérification simple
  const errors = {};
  if (!name) errors.name = "Name is required";
  if (!email) errors.email = "Email is required";
  if (!password) errors.password = "Password is required";
  if (password !== password_confirmation) errors.password_confirmation = "Passwords do not match";
  if (!role) errors.role = "Role is required";
  if (!telephone) errors.telephone = "Telephone is required";

  if (Object.keys(errors).length > 0) {
    return res.render("auth/register", {
      errors,
      old: req.body,
      csrfToken: req.csrfToken(),
    });
  }

  try {
    // Vérifier si l'utilisateur existe déjà
    const existingUser = await User.findOne({ where: { email } });
    if (existingUser) {
      errors.email = "Email already taken";
      return res.render("auth/register", {
        errors,
        old: req.body,
        csrfToken: req.csrfToken(),
      });
    }

    // Hash du mot de passe
    const hashedPassword = await bcrypt.hash(password, 10);

    // Création de l'utilisateur
    const newUser = await User.create({
      name,
      email,
      password: hashedPassword,
      role,
      telephone,
    });

    // Stocker l'utilisateur en session (sans mot de passe)
    req.session.user = {
      id: newUser.id,
      name: newUser.name,
      email: newUser.email,
      role: newUser.role,
    };

    // Redirection selon le rôle
    if (newUser.role === "admin") {
      return res.redirect("/admin/dashboard");
    }
    if (newUser.role === "service_client") {
      return res.redirect("/support/dashboard");
    }
    if (newUser.role === "livreur") {
      return res.redirect("/livreur/dashboard");
    }

    // Par défaut
    res.redirect("/");
  } catch (err) {
    console.error(err);
    res.status(500).send("Server Error");
  }
};
