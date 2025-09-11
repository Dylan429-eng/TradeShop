const crypto = require('crypto');
const pool = require('../../server/db');
const sendResetLinkEmail = require('../../utils/sendResetLinkEmail');

exports.create = (req, res) => {
  res.json({ message: 'Afficher formulaire forgot-password (API JSON)' });
};

exports.store = async (req, res) => {
  const { email } = req.body;
  if (!email) return res.status(422).json({ error: 'Email requis' });

  try {
    // Vérifier si l'utilisateur existe
    const result = await pool.query(`SELECT * FROM users WHERE email=$1`, [email]);
    const user = result.rows[0];
    if (!user) return res.status(404).json({ error: 'Utilisateur non trouvé' });

    // Générer un token aléatoire
    const token = crypto.randomBytes(32).toString('hex');

    // Stocker le token
    await pool.query(
      `INSERT INTO password_resets (email, token) VALUES ($1, $2)`,
      [email, token]
    );

    // Envoyer l'email
    await sendResetLinkEmail(user, token);

    res.json({ status: 'reset-link-sent' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
};
