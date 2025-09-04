@extends('layouts.app')

@section('title', 'Statistiques Produits')
<link rel="stylesheet" href="{{ asset('css/statistiques/index.css') }}">
@section('content')
<div class="min-h-screen">
    <div class="max-w-5xl mx-auto px-4">
        <h1 class="header-title">Statistiques des Produits</h1>
        <form method="GET" class="filters" style="margin-bottom:2rem;">
            <label for="periode">Période :</label>
            <select name="periode" id="periode" onchange="this.form.submit()" class="form-select" style="max-width:220px;">
                <option value="mois_actuel" {{ $periode == 'mois_actuel' ? 'selected' : '' }}>Ce mois-ci</option>
                <option value="mois_precedent" {{ $periode == 'mois_precedent' ? 'selected' : '' }}>Le mois dernier</option>
                <option value="deux_derniers_mois" {{ $periode == 'deux_derniers_mois' ? 'selected' : '' }}>Les deux derniers mois</option>
                <option value="semaine_actuelle" {{ $periode == 'semaine_actuelle' ? 'selected' : '' }}>Cette semaine</option>
                <option value="semaine_precedente" {{ $periode == 'semaine_precedente' ? 'selected' : '' }}>La semaine dernière</option>
                <option value="deux_derniere_semaines" {{ $periode == 'deux_derniere_semaines' ? 'selected' : '' }}>Les deux dernières semaines</option>
            </select>
        </form>

        <div class="grid-2" style="gap:2rem;">
            <div>
                <h2 class="header-title" style="font-size:1.2rem;">Top 5 produits les plus vendus</h2>
                <ul>
                    @foreach($plusVendus as $prod)
                        <li>{{ $prod->nom }} : <b>{{ $prod->ventes }}</b> ventes</li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h2 class="header-title" style="font-size:1.2rem;">Top 5 produits les moins vendus</h2>
                <ul>
                    @foreach($moinsVendus as $prod)
                        <li>{{ $prod->nom }} : <b>{{ $prod->ventes }}</b> ventes</li>
                    @endforeach
                </ul>
            </div>
        </div>

        <div style="margin-top:3rem;">
            <h2 class="header-title" style="font-size:1.2rem;">Graphique des ventes par produit</h2>
            <canvas id="produitsChart" height="120"></canvas>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const ctx = document.getElementById('produitsChart').getContext('2d');
const produitsLabels = {!! json_encode($produitsStats->pluck('nom')) !!};
const produitsVentes = {!! json_encode($produitsStats->pluck('ventes')) !!};

new Chart(ctx, {
    type: 'bar',
    data: {
        labels: produitsLabels,
        datasets: [{
            label: 'Ventes',
            data: produitsVentes,
            backgroundColor: '#2563eb',
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: { display: false }
        },
        scales: {
            x: { title: { display: true, text: 'Produit' } },
            y: { title: { display: true, text: 'Nombre de ventes' }, beginAtZero: true }
        }
    }
});
</script>
@endsection