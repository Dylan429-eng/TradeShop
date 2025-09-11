const pool = require('../../server/db');
const bcrypt = require('bcryptjs');

exports.show = (req, res) => {
  res.json({ message: 'Render confirm password view (API JSON mode)' });
};

exports.store = async (req, res) => {
  const userId = req.session.userId;
  const { password } = req.body;

  try {
    const result = await pool.query(`SELECT * FROM users WHERE id=$1`, [userId]);
    const user = result.rows[0];

    if (!user) return res.status(401).json({ error: 'Utilisateur non trouvé' });

    const valid = await bcrypt.compare(password, user.password);
    if (!valid) return res.status(422).json({ error: 'Mot de passe incorrect' });

    req.session.password_confirmed_at = Date.now();
    res.json({ redirect: '/admin/dashboard' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
};
