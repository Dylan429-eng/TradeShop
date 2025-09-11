// middlewares/signed.js
const crypto = require('crypto');

exports.validateSignature = (req, res, next) => {
  const { id, hash } = req.params;
  const secret = process.env.SIGN_SECRET || 'secret';

  const expectedHash = crypto.createHmac('sha256', secret)
                             .update(String(id))
                             .digest('hex');

  if (expectedHash !== hash) {
    return res.status(403).json({ error: 'Lien invalide ou expiré.' });
  }
  next();
};
