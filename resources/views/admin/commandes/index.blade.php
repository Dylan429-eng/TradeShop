@extends('layouts.app')

@section('title', 'Gestion des Commandes - Admin')

<link rel="stylesheet" href="{{ asset('css/commandes/index.css') }}">

@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4">
        <div class="header-flex" style="margin-bottom:2rem;">
            <div>
                <h1 class="header-title">Gestion des Commandes</h1>
                <p class="header-desc">Visualisez, assignez et gérez les commandes passées</p>
            </div>
            <div>
               
            <button onclick="document.getElementById('retrait-modal').style.display='block'" class="btn-add">
                Retrait MOMO/OM
            </button>

            </div>
        <div id="retrait-modal" style="display:none; position:fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000;">
                <div style="background:#fff; max-width:400px; margin:10vh auto; padding:2rem; border-radius:8px; position:relative;">
            <form method="POST" action="{{ route('admin.commandes.retrait') }}">
            @csrf
            <h2 class="text-lg font-bold mb-4">Retrait TradeShop</h2>
            <div class="alert-warning" style="background:#fffbe6; color:#b45309; border:1px solid #fde68a; padding:8px 12px; border-radius:4px; margin-bottom:1rem; font-size:0.95em;">
                <strong>Attention :</strong> Des frais de <b>5%</b> seront automatiquement déduits du montant saisi lors de la transaction.
            </div>
            <label>Numéro de téléphone (MOMO/OM):</label>
            <input type="text" name="phone_number" class="form-input mb-2" placeholder="+2376XXXXXXXX"required><br>
            <label>Montant à retirer (FCFA):</label>
            <input type="number" name="amount" class="form-input mb-4"  required>
            <div class="flex justify-end space-x-2">
                <button type="button" onclick="document.getElementById('retrait-modal').style.display='none'" class="btn-cancel">Annuler </button>
                <button type="submit" class="btn-add">Valider le retrait</button>
            </div>
            </form>
        </div>
    </div>
     </div>

        @if(session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="form-error">{{ session('error') }}</div>
        @endif

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Client</th>
                        <th>Produits</th>
                        <th>Montant</th>
                        <th>Date</th>
                        <th>Livreur</th>
                        <th>Statut</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($commandes as $commande)
                        <tr>
                            <td>{{ $commande->id }}</td>
                            <td>{{ $commande->client->name }}</td>
                            <td>
                                @foreach($commande->produits as $produit)
                                    <span class="badge">{{ $produit->nom }}</span>
                                @endforeach
                            </td>
                            <td style="font-weight:600;">{{ number_format($commande->total_prix, 0) }} FCFA</td>
                            <td>{{ $commande->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($commande->livreur)
                                    <span class="badge badge-green">{{ $commande->livreur->name }}</span>
                                @else
                                    <form method="POST" action="{{ route('admin.commandes.assigner', $commande->id) }}">
                                        @csrf
                                        <select name="livreur_id" class="form-select" required>
                                            <option value="">Choisir un livreur</option>
                                            @foreach($livreurs as $livreur)
                                                <option value="{{ $livreur->id }}">{{ $livreur->name }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn-add" style="margin-top:4px;">Assigner</button>
                                    </form>
                                @endif
                            </td>
                            <td>
                                <span class="badge {{ $commande->statut == 'livré' ? 'badge-green' : ($commande->statut == 'en attente' ? 'badge-yellow' : 'badge-red') }}">
                                    {{ ucfirst($commande->statut) }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.commandes.show', $commande->id) }}" class="action-btn" title="Voir détails">
                                    <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="empty-state">
                                <svg style="width:48px;height:48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                </svg>
                                <p class="empty-state-title">Aucune commande trouvée</p>
                                <p class="empty-state-desc">Aucune commande n’a été passée pour le moment.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection