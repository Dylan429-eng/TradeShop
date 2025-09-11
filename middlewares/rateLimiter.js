// middlewares/rateLimiter.js
const rateLimiters = new Map(); // clé = email, valeur = { count, lastAttempt }

const MAX_ATTEMPTS = 5;
const WINDOW_MS = 15 * 60 * 1000; // 15 minutes

exports.loginThrottle = (req, res, next) => {
  // Sécuriser l'accès à req.body
  const email = (req.body?.email || '').toLowerCase().trim();

  // Si email vide, on passe au next pour éviter crash
  if (!email) return next();

  const now = Date.now();

  if (!rateLimiters.has(email)) {
    rateLimiters.set(email, { count: 0, lastAttempt: now });
  }

  const data = rateLimiters.get(email);

  // Réinitialiser après la fenêtre
  if (now - data.lastAttempt > WINDOW_MS) {
    data.count = 0;
    data.lastAttempt = now;
  }

  data.count++;
  data.lastAttempt = now;

  if (data.count > MAX_ATTEMPTS) {
    const retryAfter = Math.ceil((WINDOW_MS - (now - data.lastAttempt)) / 1000);
    return res.status(429).json({
      error: `Trop de tentatives. Réessayez dans ${retryAfter} secondes.`,
    });
  }

  rateLimiters.set(email, data);
  next();
};
