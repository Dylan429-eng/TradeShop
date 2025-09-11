const bcrypt = require('bcryptjs');
const pool = require('../../server/db'); // PG Pool

exports.resetPassword = async (req, res) => {
  const { email, token, password } = req.body;

  try {
    const result = await pool.query(
      `SELECT * FROM password_resets WHERE email=$1 AND token=$2`,
      [email, token]
    );

    if (result.rows.length === 0) {
      return res.status(400).json({ error: "Token invalide ou expiré" });
    }

    // Hash du nouveau mot de passe
    const hashedPassword = await bcrypt.hash(password, 10);

    // Mettre à jour le mot de passe
    await pool.query(
      `UPDATE users SET password=$1 WHERE email=$2`,
      [hashedPassword, email]
    );

    // Supprimer le token pour éviter réutilisation
    await pool.query(
      `DELETE FROM password_resets WHERE email=$1`,
      [email]
    );

    res.json({ message: "Mot de passe réinitialisé avec succès" });
  } catch (err) {
    console.error(err);
    res.status(500).json({ error: "Erreur serveur" });
  }
};
