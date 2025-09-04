# TradeShop - Application E-commerce Laravel

Bienvenue sur le dépôt de TradeShop, une application e-commerce complète développée avec Laravel 12.  
Ce projet propose une gestion avancée des produits, commandes, clients, employés, livreurs et statistiques, avec une interface moderne et responsive.

## Fonctionnalités principales

- **Gestion des produits** : Ajout, modification, suppression, catégories.
- **Gestion des commandes** : Suivi, assignation à livreur, retrait, historique.
- **Gestion des clients** : Visualisation, statistiques, historique de commandes.
- **Gestion des employés** : Visualisation, suppression, rôles (admin, vendeur, livreur).
- **Gestion des livreurs** : Dashboard dédié, suivi des livraisons, commandes assignées.
- **Statistiques avancées** : Produits les plus/moins vendus, filtres par période, graphiques Chart.js.
- **Notifications** : Envoi d’email Gmail au livreur lors de l’assignation d’une commande.
- **Sécurité** : Authentification, vérification email, middlewares par rôle.
- **Responsive design** : Interface adaptée desktop et mobile.

## Installation

1. **Cloner le dépôt**
   ```bash
   git clone https://github.com/votre-utilisateur/tradeshop.git
   cd tradeshop
   ```

2. **Installer les dépendances**
   ```bash
   composer install
   npm install && npm run build
   ```

3. **Configurer l’environnement**
   - Copier `.env.example` en `.env`
   - Renseigner vos accès DB et Gmail (pour les notifications)

4. **Générer la clé d’application**
   ```bash
   php artisan key:generate
   ```

5. **Migrer la base de données**
   ```bash
   php artisan migrate --seed
   ```

6. **Lancer le serveur**
   ```bash
   php artisan serve
   ```

## Configuration Gmail (notifications)

Dans `.env` :
```
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your_gmail@gmail.com
MAIL_PASSWORD=your_gmail_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your_gmail@gmail.com
MAIL_FROM_NAME="TradeShop"
```

## Structure des rôles

- **Admin** : Accès complet à la gestion et aux statistiques.
- **Livreur** : Dashboard personnel, accès aux commandes assignées.
- **Vendeur/Employé** : Gestion des produits et commandes selon le rôle.

## Contribution

Les PR et suggestions sont les bienvenues !  
Merci de respecter la structure du projet et d’ajouter des tests si possible.

## Licence

Ce projet est sous licence MIT.

---

**TradeShop** – E-commerce Laravel, complet et évolutif.
