const sendResetLinkEmail = require('../../utils/sendResetLinkEmail'); // À créer
const pool = require('../../server/db');

exports.create = (req, res) => {
  res.json({ message: 'Afficher formulaire forgot-password (API JSON)' });
};

exports.store = async (req, res) => {
  const { email } = req.body;
  if (!email) return res.status(422).json({ error: 'Email requis' });

  try {
    const result = await pool.query(`SELECT * FROM users WHERE email=$1`, [email]);
    const user = result.rows[0];
    if (!user) return res.status(404).json({ error: 'Utilisateur non trouvé' });

    await sendResetLinkEmail(user); // Fonction utilitaire pour envoyer l’email
    res.json({ status: 'reset-link-sent' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
};
