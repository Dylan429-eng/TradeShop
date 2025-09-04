@extends('layouts.app')

@section('title', 'Dashboard Livreur')

@section('content')
<div class="dashboard-wrapper">
    <div class="dashboard-container">
        <!-- En-tête -->
        <div class="dashboard-header">
            <h1>Bienvenue, {{ auth()->user()->name }}</h1>
            <p>Suivi de vos livraisons et commandes assignées</p>
        </div>

        <!-- Navigation par onglets -->
        <ul class="tab-nav">
            <li class="active" data-tab="stats">📊 Statistiques</li>
            <li data-tab="encours">🚚 Commandes à livrer</li>
            <li data-tab="livrees">✅ Commandes livrées</li>
        </ul>

        <!-- Contenu des onglets -->
        <div class="tab-content active" id="stats">
            <div class="grid grid-1-3">
                <div class="card">
                    <div class="card-content">
                        <div class="card-icon">✅</div>
                        <div>
                            <h2>{{ $commandesJourLivrees }}</h2>
                            <p>Commandes livrées aujourd'hui</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="card-icon">🕒</div>
                        <div>
                            <h2>{{ $commandesAssignes }}</h2>
                            <p>Commandes à livrer</p>
                        </div>
                    </div>
                </div>
                <div class="card">
                    <div class="card-content">
                        <div class="card-icon">📦</div>
                        <div>
                            <h2>{{ $totalCommandes }}</h2>
                            <p>Total commandes assignées</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="tab-content" id="encours">
            <div class="card">
                <h3>Commandes à livrer</h3>
                @if($commandesEnCours->count() > 0)
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Adresse</th>
                                    <th>Produits</th>
                                    <th>Statut</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commandesEnCours as $commande)
                                <tr>
                                    <td>{{ $commande->client->name }}</td>
                                    <td>{{ $commande->client->lieu }}</td>
                                    <td>
                                        @foreach($commande->produits as $prod)
                                            <span class="badge">{{ $prod->nom }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        <span class="badge {{ $commande->statut == 'livré' ? 'bg-green' : 'bg-yellow' }}">
                                            {{ ucfirst($commande->statut) }}
                                        </span>
                                    </td>
                                    <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                    @if($commande->statut != 'livré')
                                     <form action="{{ route('livreur.commandes.confirmer', $commande->id) }}" method="POST">
                                        @csrf
                                         <button type="submit" class="btn btn-success">
                                        Confirmer
                                        </button>
                                    </form>
                                    @else
                                    <span class="badge bg-green">Déjà livrée</span>
                                     @endif
                                     </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>Aucune commande à livrer</p>
                @endif
            </div>
        </div>

        <div class="tab-content" id="livrees">
            <div class="card">
                <h3>Commandes livrées aujourd'hui</h3>
                @if($commandesJour->count() > 0)
                    <div class="table-wrapper">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Client</th>
                                    <th>Adresse</th>
                                    <th>Produits</th>
                                    <th>Date livraison</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($commandesJour as $commande)
                                <tr>
                                    <td>{{ $commande->client->name }}</td>
                                    <td>{{ $commande->client->lieu}}</td>
                                    <td>
                                        @foreach($commande->produits as $prod)
                                            <span class="badge">{{ $prod->nom }}</span>
                                        @endforeach
                                    </td>
                                    <td>{{ $commande->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p>Aucune commande livrée aujourd'hui</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Script onglets -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const tabs = document.querySelectorAll(".tab-nav li");
        const contents = document.querySelectorAll(".tab-content");

        tabs.forEach(tab => {
            tab.addEventListener("click", () => {
                tabs.forEach(t => t.classList.remove("active"));
                contents.forEach(c => c.classList.remove("active"));
                tab.classList.add("active");
                document.getElementById(tab.dataset.tab).classList.add("active");
            });
        });
    });
</script>

<!-- Styles responsive -->
<style>
    /* Onglets */
    .tab-nav {
        display: flex;
        flex-wrap: wrap;
        list-style: none;
        padding: 0;
        margin: 1rem 0;
        border-bottom: 2px solid #ddd;
    }
    .tab-nav li {
        flex: 1;
        text-align: center;
        padding: 0.75rem;
        cursor: pointer;
        background: #f3f4f6;
        border-radius: 8px 8px 0 0;
        font-weight: 600;
        transition: all 0.3s;
    }
    .tab-nav li.active {
        background: #2563eb;
        color: white;
    }

    /* Contenu */
    .tab-content {
        display: none;
        animation: fadeIn 0.3s ease-in-out;
    }
    .tab-content.active {
        display: block;
    }
    @keyframes fadeIn {
        from {opacity: 0;}
        to {opacity: 1;}
    }

    /* Grilles */
    .grid {
        display: grid;
        gap: 1rem;
    }
    .grid-1-3 {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }

    /* Table responsive */
    .table-wrapper {
        width: 100%;
        overflow-x: auto;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
    }
    table th, table td {
        padding: 0.5rem;
        border: 1px solid #ddd;
        text-align: left;
    }
    .btn {
    padding: 0.4rem 0.8rem;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 0.85rem;
    font-weight: 600;
}
.btn-success {
    background: #16a34a;
    color: white;
    transition: background 0.2s;
}
.btn-success:hover {
    background: #15803d;
}


    /* Mobile */
    @media (max-width: 768px) {
        .dashboard-header h1 {
            font-size: 1.25rem;
        }
        .dashboard-header p {
            font-size: 0.9rem;
        }
        .tab-nav li {
            font-size: 0.85rem;
            padding: 0.5rem;
        }
        table th, table td {
            font-size: 0.8rem;
            padding: 0.4rem;
        }
    }
</style>
@endsection
