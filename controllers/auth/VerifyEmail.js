const pool = require('../../server/db');

exports.verify = async (req, res) => {
  const userId = req.session.userId;

  try {
    const result = await pool.query(`SELECT * FROM users WHERE id=$1`, [userId]);
    const user = result.rows[0];
    if (!user) return res.status(401).json({ error: 'Utilisateur non trouvé' });

    if (user.email_verified)
      return res.json({ redirect: '/admin/dashboard?verified=1' });

    await pool.query(`UPDATE users SET email_verified=true WHERE id=$1`, [userId]);

    res.json({ redirect: '/admin/dashboard?verified=1' });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: 'Erreur serveur' });
  }
};
