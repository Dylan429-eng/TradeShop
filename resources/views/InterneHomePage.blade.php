<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Interface Interne – TradeShop</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="{{ asset('css/HomePage.css') }}">
</head>
<body>

    <header>
       <div class="container nav">
            <h1>🛒 TradeShop</h1>
            <div>
                <a href="/login">Connexion</a>
                <a href="/register">Inscription</a>
            </div>
        </div>
    </header>

    <section class="hero hero-internal">
        <h2>Bienvenue sur TradeShop</h2>
        <p>Gérez efficacement vos opérations avec TradeShop.</p>
    </section>

    <section class="features">
        <div class="container">
            <h3>Fonctionnalités disponibles</h3>
            <div class="feature-grid">
                <div class="feature">
                    <img src="https://img.icons8.com/color/96/combo-chart--v1.png" alt="Dashboard">
                    <h4>Tableau de bord</h4>
                    <p>Statistiques en temps réel sur les ventes, utilisateurs, livraisons.</p>
                </div>
                <div class="feature">
                    <img src="https://img.icons8.com/color/96/product.png" alt="Produits">
                    <h4>Gestion des produits</h4>
                    <p>Ajoutez, modifiez ou supprimez les articles du catalogue.</p>
                </div>
                <div class="feature">
                    <img src="https://img.icons8.com/color/96/order-history.png" alt="Commandes">
                    <h4>Suivi des commandes</h4>
                    <p>Visualisez, traitez et expedier les commandes client.</p>
                </div>
                <div class="feature">
                    <img src="https://img.icons8.com/color/96/group.png" alt="Utilisateurs">
                    <h4>Utilisateurs</h4>
                    <p>Gérez les accès et rôles : clients, livreurs, support, vendeurs.</p>
                </div>
                <div class="feature">
                    <img src="https://img.icons8.com/color/96/settings.png" alt="Paramètres">
                    <h4>Paramètres</h4>
                    <p>Configuration système, paiements sécurisée .</p>
                </div>
            </div>
        </div>
    </section>

    <footer>
        &copy; {{ date('Y') }} TradeShop  Tous droits réservés.
    </footer>

</body>
</html>
