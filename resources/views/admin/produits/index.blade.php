
@extends('layouts.app')

@section('title', 'Gestion des Produits - TradeShop')
<link rel="stylesheet" href="{{asset('css/produits/index.css')}}">
@section('content')
<div class="min-h-screen">
    <div class="max-w-7xl mx-auto px-4">
        <!-- Header -->
        <div class="header-flex">
            <div>
                <h1 class="header-title">Gestion des Produits</h1>
                <p class="header-desc">Gérez votre catalogue de produits</p>
            </div>
            <a href="{{ route('admin.produits.create') }}" class="btn-add">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Ajouter un produit
            </a>
        </div>

        <!-- Messages de succès -->
        @if(session('success'))
            <div class="success-msg">
                {{ session('success') }}
            </div>
        @endif

        <!-- Filtres -->
        <div class="filters">
            <form method="GET" class="filters" style="gap: 1rem;">
                <div style="flex:1; min-width: 200px;">
                    <label>Rechercher</label>
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Nom du produit...">
                </div>
                <div style="min-width: 160px;">
                    <label>Catégorie</label>
                    <select name="categorie">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $categorie)
                            <option value="{{ $categorie->id }}" {{ request('categorie') == $categorie->id ? 'selected' : '' }}>
                                {{ $categorie->type }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div style="display: flex; align-items: flex-end;">
                    <button type="submit">Filtrer</button>
                </div>
            </form>
        </div>
    <div class="form-container" style="margin-bottom:2rem;">
        <form method="POST" action="{{ route('admin.categories.store') }}" style="display:flex;gap:1rem;align-items:center;">
            @csrf
            <input type="text" name="type" placeholder="Nouvelle catégorie..." required class="form-input" style="max-width:250px;">
            <button type="submit" class="btn-add">
            <svg style="width:20px;height:20px;margin-right:8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Ajouter
            </button>
         </form>
        @if(session('cat_success'))
            <div class="success-msg" style="margin-top:1rem;">
                {{ session('cat_success') }}
            </div>
        @endif
        @error('type')
        <div class="form-error">{{ $message }}</div>
        @enderror
    </div>

        <!-- Liste des produits -->
        <div class="table-container">
            <div style="overflow-x:auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Produit</th>
                            <th>Catégorie</th>
                            <th>Prix</th>
                            <th>Stock</th>
                            <th>Créé par</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($produits as $produit)
                            <tr>
                                <td>
                                    @if($produit->image)
                                        <img src="{{ asset($produit->image) }}" alt="{{ $produit->nom }}" class="img-thumb">
                                    @else
                                        <div class="img-thumb" style="display:flex;align-items:center;justify-content:center;">
                                            <svg style="width:32px;height:32px;color:#9ca3af;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div style="font-weight:500;color:#22223b;">{{ $produit->nom }}</div>
                                    <div style="color:#4a5568;max-width:200px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">{{ $produit->description }}</div>
                                </td>
                                <td>
                                    <span class="badge">{{ $produit->categorie->type }}</span>
                                </td>
                                <td style="font-weight:600;color:#22223b;">
                                    {{ number_format($produit->prix, 2) }} FCFA
                                </td>
                                <td>
                                    <span class="badge
                                        {{ $produit->stock > 10 ? 'badge-green' : ($produit->stock > 0 ? 'badge-yellow' : 'badge-red') }}">
                                        {{ $produit->stock }} en stock
                                    </span>
                                </td>
                                <td style="color:#4a5568;">
                                    {{ $produit->user->name }}
                                </td>
                                <td>
                                    <div style="display:flex;gap:8px;">
                                        <a href="{{ route('admin.produits.edit', $produit->id) }}" class="action-btn" title="Modifier">
                                            <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('admin.produits.delete', $produit->id) }}" style="display:inline;" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce produit ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="action-btn delete" title="Supprimer">
                                                <svg style="width:20px;height:20px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="empty-state">
                                    <svg style="width:48px;height:48px;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2 2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                    </svg>
                                    <p class="empty-state-title">Aucun produit trouvé</p>
                                    <p class="empty-state-desc">Commencez par ajouter votre premier produit.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($produits->hasPages())
                <div class="pagination">
                    {{ $produits->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection