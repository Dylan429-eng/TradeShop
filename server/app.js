const express = require('express');
const cors = require('cors');
const session = require('express-session');
const flash = require('connect-flash');
const path = require('path');
const csurf = require('csurf');
const expressLayouts = require('express-ejs-layouts');
const methodOverride = require('method-override');
const pgSession = require('connect-pg-simple')(session);
require('dotenv').config();

const app = express();

// --- Middlewares globaux ---
app.use(cors());
app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(expressLayouts);
app.use(methodOverride('_method'));
// --- Session obligatoire pour CSRF ---
app.use(
  session({
    store: new pgSession({
      conObject: {
        conString: process.env.DATABASE_URL,
        ssl: {
          require: true,
          rejectUnauthorized: false, // << clé pour Render + Supabase
        },
      },
      tableName: 'sessions',
      schemaName: 'public',
    }),
    secret: process.env.SESSION_SECRET,
    resave: false,
    saveUninitialized: false,
  })
);
app.use(flash());

// --- CSRF protection (global, sauf certaines routes) ---
const csrfProtection = csurf();

app.use((req, res, next) => {
  // Désactiver CSRF UNIQUEMENT sur /admin/produitStore
  if (req.path === '/admin/produitStore'|| req.path.startsWith('/admin/produitUpdate/')) {
    return next();
  }
  csrfProtection(req, res, next);
});

// --- Middleware pour exposer token CSRF et utilisateur aux vues ---
app.use((req, res, next) => {
  try {
    res.locals.csrfToken = req.csrfToken ? req.csrfToken() : null; // token CSRF si dispo
  } catch (e) {
    res.locals.csrfToken = null;
  }
  res.locals.user = req.user || null;
  res.locals.success = req.flash("success");
  res.locals.error = req.flash("error");
  next();
});

// --- Config moteur de template ---
app.set('view engine', 'ejs');
app.set('views', path.join(__dirname, '../views'));
app.set('layout', 'layouts/app');

// --- Fichiers statiques ---
app.use(express.static(path.join(__dirname, '../public')));
app.use('/uploads', express.static(path.join(__dirname, '../public/uploads')));

// --- Routes principales ---
const adminRoutes = require('../routes/admin');
const livreurRoutes = require('../routes/livreur');
const authRoutes = require('../routes/auth');

app.use('/admin', adminRoutes);
app.use('/livreur', livreurRoutes);
app.use('/auth', authRoutes);

// --- Page d’accueil ---
app.get('/', (req, res) => {
  res.render('InterneHomePage');
});

// --- Gestion des erreurs CSRF ---
app.use((err, req, res, next) => {
  if (err.code === 'EBADCSRFTOKEN') {
    return res.status(403).send('Formulaire expiré ou invalide.');
  }
  next(err);
});

// --- Lancement serveur ---
const PORT = process.env.PORT || 3000;
app.listen(PORT, () => console.log(`✅ Server running on port ${PORT}`));

module.exports = app;