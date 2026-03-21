@extends('layouts.app', ['title' => 'Réinitialisation'])

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h3">Demander un lien de réinitialisation</h1>
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Pseudo</label>
                        <input name="pseudo" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input name="mail" type="email" class="form-control" required>
                    </div>
                    <button class="btn btn-success">Envoyer le lien</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
