const pool = require('../../server/db');
const bcrypt = require('bcryptjs');

exports.update = async (req, res) => {
  const userId = req.session.userId;
  const { current_password, password, password_confirmation } = req.body;

  if (!password || password !== password_confirmation)
    return res.status(422).json({ error: 'Validation échouée' });

  try {
    const result = await pool.query(`SELECT * FROM users WHERE id=$1`, [userId]);
    const user = result.rows[0];
    if (!user) return res.status(401).json({ error: 'Utilisateur non trouvé' });

    const valid = await bcrypt.compare(current_password, user.password);
    if (!valid) return res.status(422).json({ error: 'Mot de passe actuel incorrect' });

    const hashed = await bcrypt.hash(password, 12);
    await pool.query(`UPDATE users SET password=$1 WHERE id=$2`, [hashed, userId]);

    res.json({ status: 'password-updated' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
};
