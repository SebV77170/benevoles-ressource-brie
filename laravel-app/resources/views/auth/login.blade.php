@extends('layouts.app', ['title' => 'Connexion'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h3 mb-4">Connexion</h1>
                <form method="POST" action="{{ route('login.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pseudo</label>
                        <input name="pseudo" class="form-control" value="{{ old('pseudo') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mot de passe</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="form-check mb-3">
                        <input type="checkbox" name="remember" value="1" class="form-check-input">
                        <label class="form-check-label">Se souvenir de moi</label>
                    </div>
                    <button class="btn btn-success w-100">Se connecter</button>
                </form>
                <div class="mt-3 text-center">
                    <a href="{{ route('password.request') }}">Mot de passe oublié ?</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
