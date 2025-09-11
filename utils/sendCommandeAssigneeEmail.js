const nodemailer = require('nodemailer');
require('dotenv').config();

async function sendCommandeAssigneeEmail(livreur, commande) {
  const dashboardUrl = `${process.env.FRONTEND_URL}/livreur/dashboard`;

  const transporter = nodemailer.createTransport({
    host: process.env.MAIL_HOST,
    port: parseInt(process.env.MAIL_PORT, 10),
    secure: process.env.MAIL_ENCRYPTION === 'ssl', // true si SSL, false si TLS
    auth: {
      user: process.env.MAIL_USERNAME,
      pass: process.env.MAIL_PASSWORD,
    },
  });

  const mailOptions = {
    from: `"${process.env.MAIL_FROM_NAME}" <${process.env.MAIL_FROM_ADDRESS}>`,
    to: livreur.email,
    subject: 'Nouvelle commande à livrer',
    html: `
      <p>Bonjour <strong>${livreur.name}</strong>,</p>
      <p>Une nouvelle commande (#${commande.id}) vous a été assignée.</p>
      <p>
        <a href="${dashboardUrl}" 
           style="display:inline-block;padding:10px 20px;background:#2563eb;color:#fff;text-decoration:none;border-radius:6px;">
          Voir la commande
        </a>
      </p>
      <p>Merci de traiter cette livraison rapidement.</p>
    `,
  };

  await transporter.sendMail(mailOptions);
  console.log(`📩 Email de commande assignée envoyé à ${livreur.email}`);
}

module.exports = sendCommandeAssigneeEmail;
