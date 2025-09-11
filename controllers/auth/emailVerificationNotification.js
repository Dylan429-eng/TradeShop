const sendVerificationEmail = require('../../utils/sendVerificationEmail'); // À créer
const pool = require('../../server/db');

exports.store = async (req, res) => {
  const userId = req.session.userId;

  try {
    const result = await pool.query(`SELECT * FROM users WHERE id=$1`, [userId]);
    const user = result.rows[0];
    if (!user) return res.status(401).json({ error: 'Utilisateur non trouvé' });

    if (user.email_verified) return res.json({ redirect: '/admin/dashboard' });

    try {
      await sendVerificationEmail(user); // Fonction pour envoyer l’email
      res.json({ status: 'verification-link-sent' });
    } catch (e) {
      console.error('Erreur email', e);
      res.status(500).json({ error: 'Impossible d’envoyer l’email' });
    }
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
};
