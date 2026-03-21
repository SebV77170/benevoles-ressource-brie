@extends('layouts.app', ['title' => 'Bénévoles'])

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h2">Bénévoles</h1>
        <table class="table table-striped align-middle">
            <thead><tr><th>Nom</th><th>Pseudo</th><th>Mail</th><th>Rôle</th><th>Inscription</th></tr></thead>
            <tbody>
            @foreach($volunteers as $volunteer)
                <tr>
                    <td>{{ $volunteer->prenom }} {{ $volunteer->nom }}</td>
                    <td>{{ $volunteer->pseudo }}</td>
                    <td>{{ $volunteer->mail }}</td>
                    <td>{{ $volunteer->admin }}</td>
                    <td>{{ optional($volunteer->profileDates?->date_inscription)->format('d/m/Y H:i') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
