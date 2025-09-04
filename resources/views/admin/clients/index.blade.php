@extends('layouts.app')

@section('title', 'Liste des Clients')
<link rel="stylesheet" href="{{ asset('css/commandes/index.css') }}">

@section('content')
<div class="min-h-screen">
    <div class="max-w-5xl mx-auto px-4">
            <a href="{{ route('admin.users') }}" class="btn-add">
                
                Visualiser les Employés
            </a>
        <h1 class="header-title">Liste des Clients</h1>
        <table class="table-container">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Nbre_Commandes</th>
                    <th>Date d’inscription</th>
                    <th>Ville</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clients as $client)
                    <tr>
                        <td>{{ $client->id }}</td>
                        <td>{{ $client->name }}</td>
                        <td>{{ $client->email }}</td>
                        <td>{{ $client->telephone }}</td>
                        <td>{{ $client->commandes_count }}</td>
                        <td>{{ $client->created_at->format('d/m/Y') }}</td>
                        <td>{{$client->lieu}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty-state">Aucun client trouvé.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        <div class="pagination">
            {{ $clients->links() }}
        </div>
    </div>
</div>



@endsection