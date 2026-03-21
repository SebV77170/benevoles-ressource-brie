@extends('layouts.app', ['title' => 'Compte en attente'])

@section('content')
<div class="alert alert-warning shadow-sm">
    <h1 class="h3">Compte en attente de validation</h1>
    <p>Votre inscription existe bien, mais elle doit être validée par un administrateur avant l'accès aux modules internes.</p>
</div>
@endsection
