exports.prompt = (req, res) => {
  const user = req.session.user;
  if (!user) return res.status(401).json({ error: 'Non authentifié' });

  if (user.email_verified) {
    if (user.role === 'livreur') return res.json({ redirect: '/livreur/dashboard' });
    return res.json({ redirect: '/admin/dashboard' });
  }

  res.json({ message: 'Email non vérifié, afficher prompt' });
};
