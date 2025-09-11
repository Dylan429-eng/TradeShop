const pool = require('../../server/db');
const bcrypt = require('bcryptjs');

exports.create = (req, res) => {
  res.json({ message: 'API pour afficher le reset password view' });
};

exports.store = async (req, res) => {
  const { email, password, password_confirmation, token } = req.body;

  if (!email || !password || password !== password_confirmation)
    return res.status(422).json({ error: 'Validation échouée' });

  // Vérifier le token ici selon ton implémentation
  try {
    const result = await pool.query(`SELECT * FROM users WHERE email=$1`, [email]);
    const user = result.rows[0];
    if (!user) return res.status(404).json({ error: 'Utilisateur non trouvé' });

    const hashed = await bcrypt.hash(password, 12);
    await pool.query(`UPDATE users SET password=$1 WHERE id=$2`, [hashed, user.id]);

    res.json({ status: 'password-reset-success' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
};
