
exports.isAuthenticated = (req, res, next) => {
  if (req.session && req.session.user) {
    req.user = req.session.user; // dispo partout
    return next();
  }

  // Option 1 : redirection vers une page de login
  req.flash('error', 'Vous devez être connecté pour accéder à cette page.');
  return res.redirect('/auth/login');
};

/**
 * Middleware "isGuest"
 * Si l'utilisateur est déjà connecté, redirige selon son rôle.
 */
exports.isGuest = (req, res, next) => {
    if (!req.session || !req.session.user) return next();

    // L'utilisateur est connecté, redirection selon son rôle
    const role = req.session.user.role;
    switch (role) {
        case 'admin':
        case 'super_admin':
            return res.redirect('/admin/dashboard');
        case 'livreur':
            return res.redirect('/livreur/dashboard');
        
        default:
            return res.redirect('/'); // fallback
    }
};


