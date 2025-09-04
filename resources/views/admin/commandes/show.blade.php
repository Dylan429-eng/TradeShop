@extends('layouts.app')

@section('title', 'Détail Commande')

@section('content')
<div class="max-w-3xl mx-auto mt-8 bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Détail de la commande #{{ $commande->id }}</h1>
    <p><strong>Client :</strong> {{ $commande->client->name ?? '-' }}</p>
    <p><strong>Montant :</strong> {{ number_format($commande->total_prix, 0) }} FCFA</p>
    <p><strong>Date :</strong> {{ $commande->created_at->format('d/m/Y H:i') }}</p>
    <p><strong>Statut :</strong> {{ ucfirst($commande->statut) }}</p>
    <p><strong>Livreur :</strong> {{ $commande->livreur ? $commande->livreur->name : 'Non assigné' }}</p>
    <h2 class="mt-4 font-semibold">Produits :</h2>
    <ul>
        @foreach($commande->commandeDetails as $detail)
            <li>
                {{ $detail->produit->nom ?? 'Produit supprimé' }} - {{ $detail->quantity }} x {{ number_format($detail->prix, 0) }} FCFA
            </li>
        @endforeach
    </ul>
</div>
@endsection