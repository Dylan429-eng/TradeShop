const nodemailer = require('nodemailer');
require('dotenv').config();

/**
 * Envoie un email de vérification à un utilisateur
 * @param {Object} user - L'utilisateur { id, name, email }
 */
async function sendVerificationEmail(user) {
  const verificationLink = `${process.env.FRONTEND_URL}/verify-email?user=${user.id}`;

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
    subject: 'Vérification de votre adresse email',
    html: `
      <p>Bonjour ${user.name},</p>
      <p>Merci de vous être inscrit. Cliquez sur le bouton ci-dessous pour vérifier votre email :</p>
      <a href="${verificationLink}" style="
          display: inline-block;
          padding: 10px 20px;
          font-size: 16px;
          color: white;
          background-color: #007BFF;
          text-decoration: none;
          border-radius: 5px;
      ">Vérifier mon email</a>
      <p>Si le bouton ne fonctionne pas, copiez-collez ce lien dans votre navigateur : ${verificationLink}</p>
    `,
  };

  await transporter.sendMail(mailOptions);
  console.log(`Email de vérification envoyé à ${user.email}`);
}

module.exports = sendVerificationEmail;
