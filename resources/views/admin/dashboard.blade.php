@extends('layouts.app')

@section('title', 'Dashboard Admin E-commerce')

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <h1>Bienvenue sur TradeShop</h1>
            <p>Gérez votre plateforme e-commerce depuis cette interface</p>
        </div>

        <!-- Statistiques principales -->
        <div class="grid grid-1-4">
            <div class="card">
                <div class="card-content">
                    <div class="card-icon">📦</div>
                    <div>
                        <h2>{{ $totalProduits }}</h2>
                        <p>Produits</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-icon">🛒</div>
                    <div>
                        <h2>{{ $totalCommandes }}</h2>
                        <p>Commandes</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-icon">👥</div>
                    <div>
                        <h2>{{ $totalClients }}</h2>
                        <p>Clients</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-content">
                    <div class="card-icon">💰</div>
                    <div>
                        <h2>{{ number_format($ventesTotales, 2) }} FCFA</h2>
                        <p>Ventes totales</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alertes -->
        <div class="grid grid-1-3">
            <div class="card">
                <h3>Commandes en attente</h3>
                <span class="badge bg-yellow">{{ $commandesEnAttente }}</span>
                <p>Commandes nécessitant votre attention</p>
            </div>

            <div class="card">
                <h3>Livraisons en attente</h3>
                <span class="badge bg-orange">{{ $livraisonsEnAttente }}</span>
                <p>Livraisons à programmer</p>
            </div>

            <div class="card">
                <h3>Messages non lus</h3>
                <span class="badge bg-red">{{ $messagesNonLus }}</span>
                <p>Messages clients en attente</p>
            </div>
        </div>

        <!-- Navigation rapide -->
        <!-- <div class="grid grid-1-4">
            <a href="{{ route('admin.produits') }}" class="card card-link">
                <div class="text-center">
                    <div class="card-icon">🏪</div>
                    <h3>Gestion des Produits</h3>
                    <p>Ajouter, modifier et supprimer des produits</p>
                </div>
            </a>

            <a href="{{ route('admin.commandes') }}" class="card card-link">
                <div class="text-center">
                    <div class="card-icon">📋</div>
                    <h3>Gestion des Commandes</h3>
                    <p>Suivre et gérer les commandes clients</p>
                </div>
            </a>

            <a href="{{ route('admin.clients') }}" class="card card-link">
                <div class="text-center">
                    <div class="card-icon">👤</div>
                    <h3>Gestion des Clients</h3>
                    <p>Gérer les comptes clients</p>
                </div>
            </a>

            <a href="{{ route('admin.statistiques') }}" class="card card-link">
                <div class="text-center">
                    <div class="card-icon">📊</div>
                    <h3>Statistiques</h3>
                    <p>Analyser les performances</p>
                </div>
            </a>
        </div> -->

        <!-- Commandes récentes -->
        <!-- <div class="card">
            <h3>Commandes Récentes</h3>
            @if($commandesRecentes->count() > 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th>Client</th>
                            <th>Montant</th>
                            <th>Statut</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($commandesRecentes as $commande)
                        <tr>
                            <td>{{ $commande->client->name }}</td>
                            <td>{{ $commande->formatted_total_prix }}</td>
                            <td>
                                <span class="badge 
                                    @if($commande->statut == 'livré') bg-green
                                    @elseif($commande->statut == 'en attente') bg-yellow
                                    @else bg-red @endif">
                                    {{ ucfirst($commande->statut) }}
                                </span>
                            </td>
                            <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p>Aucune commande récente</p>
            @endif
        </div> -->

        <!-- Produits populaires -->
        <!-- <div class="card">
            <h3>Produits les Plus Vendus</h3>
            @if($produitsPopulaires->count() > 0)
                @foreach($produitsPopulaires as $produit)
                <div class="produit-row">
                    <div>
                        <strong>{{ $produit->nom }}</strong>
                        <p>{{ $produit->formatted_prix }}</p>
                    </div>
                    <span class="badge bg-blue">
                        {{ $produit->commande_details_count }} ventes
                    </span>
                </div>
                @endforeach
            @else
                <p>Aucune donnée de vente disponible</p>
            @endif
        </div>
    </div>
</div> -->

<!-- Barre de navigation mobile -->
<nav class="mobile-nav">
    <div class="nav-container">
        <a href="{{ route('admin.dashboard') }}" class="nav-item active">
            <span class="nav-icon">🏠</span>
            <span class="nav-label">Accueil</span>
        </a>
        <a href="{{ route('admin.produits') }}" class="nav-item">
            <span class="nav-icon">📦</span>
            <span class="nav-label">Produits</span>
        </a>
        <a href="{{ route('admin.commandes') }}" class="nav-item">
            <span class="nav-icon">🧾</span>
            <span class="nav-label">Commandes</span>
        </a>
        <a href="{{ route('admin.clients') }}" class="nav-item">
            <span class="nav-icon">👥</span>
            <span class="nav-label">Clients</span>
        </a>
        <a href="{{ route('admin.statistiques') }}" class="nav-item">
            <span class="nav-icon">📊</span>
            <span class="nav-label">Stats</span>
        </a>
    </div>
</nav>
@endsection
