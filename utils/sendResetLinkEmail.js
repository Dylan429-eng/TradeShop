const nodemailer = require('nodemailer');
require('dotenv').config();

/**
 * Envoie un email pour réinitialiser le mot de passe
 * @param {Object} user - L'utilisateur { id, name, email }
 * @param {string} token - Le token de réinitialisation
 */
async function sendResetLinkEmail(user, token) {
  const resetLink = `${process.env.FRONTEND_URL}/reset-password?token=${token}&email=${encodeURIComponent(user.email)}`;

  const transporter = nodemailer.createTransport({
    host: process.env.MAIL_HOST,
    port: process.env.MAIL_PORT,
    secure: process.env.MAIL_SECURE === 'true',
    auth: {
      user: process.env.MAIL_USER,
      pass: process.env.MAIL_PASSWORD,
    },
  });

  const mailOptions = {
    from: `"${process.env.MAIL_FROM_NAME}" <${process.env.MAIL_FROM_EMAIL}>`,
    to: user.email,
    subject: 'Réinitialisation de votre mot de passe',
    html: `
      <p>Bonjour ${user.name},</p>
      <p>Vous avez demandé à réinitialiser votre mot de passe. Cliquez sur le bouton ci-dessous :</p>
      <a href="${resetLink}" style="
          display: inline-block;
          padding: 10px 20px;
          font-size: 16px;
          color: white;
          background-color: #28a745;
          text-decoration: none;
          border-radius: 5px;
      ">Réinitialiser mon mot de passe</a>
      <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur : ${resetLink}</p>
    `,
  };

  await transporter.sendMail(mailOptions);
  console.log(`Email de réinitialisation envoyé à ${user.email}`);
}

module.exports = sendResetLinkEmail;
