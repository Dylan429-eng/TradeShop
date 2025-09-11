

exports.isAdmin = (req, res, next) => {
  if (req.session?.role === 'admin' || req.session?.role === 'super_admin') return next();
  return res.status(403).json({ error: 'Accès non autorisé. Administrateur requis.' });
};

exports.isLivreur = (req, res, next) => {
  if (req.session?.role === 'livreur') return next();
  return res.status(403).json({ error: 'Accès interdit. Réservé aux livreurs.' });
};
