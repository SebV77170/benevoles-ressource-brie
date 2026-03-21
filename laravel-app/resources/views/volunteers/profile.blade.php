@extends('layouts.app', ['title' => 'Mon profil'])

@section('content')
<div class="row g-4">
    <div class="col-lg-5">
        <div class="card shadow-sm">
            <div class="card-body">
                <h1 class="h3">Mon profil</h1>
                <form method="POST" action="{{ route('volunteers.profile.update') }}">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="mail" value="{{ old('mail', $user->mail) }}" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Téléphone</label>
                        <input type="text" name="tel" value="{{ old('tel', $user->tel) }}" class="form-control">
                    </div>
                    <button class="btn btn-success">Enregistrer</button>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-7">
        <div class="card shadow-sm">
            <div class="card-body">
                <h2 class="h4">Mes créneaux</h2>
                <table class="table table-striped">
                    <thead><tr><th>Début</th><th>Fin</th><th>Fonction</th></tr></thead>
                    <tbody>
                    @foreach($user->eventSlots as $slot)
                        <tr>
                            <td>{{ $slot->start?->format('d/m/Y H:i') }}</td>
                            <td>{{ $slot->end?->format('d/m/Y H:i') }}</td>
                            <td>{{ $slot->pivot->fonction }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
