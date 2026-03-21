@extends('layouts.app', ['title' => 'Planning'])

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h2">Planning des créneaux</h1>
    <span class="text-muted">{{ $start->format('d/m/Y') }} → {{ $end->format('d/m/Y') }}</span>
</div>
<form method="POST" action="{{ route('planning.register') }}" class="card shadow-sm">
    @csrf
    <div class="card-body">
        @forelse($slotsByDay as $day => $slots)
            <h2 class="h5 mt-3">{{ \Carbon\Carbon::parse($day)->locale('fr')->isoFormat('dddd D MMMM YYYY') }}</h2>
            <div class="row g-3 mb-2">
                @foreach($slots as $slot)
                    <div class="col-md-6 col-lg-4">
                        <label class="border rounded p-3 d-block bg-light h-100">
                            <input type="checkbox" name="event_ids[]" value="{{ $slot['id'] }}">
                            <strong>{{ $slot['label'] }}</strong><br>
                            <span>{{ $slot['name'] }}</span><br>
                            <small>{{ $slot['volunteers'] }} bénévole(s)</small>
                        </label>
                    </div>
                @endforeach
            </div>
        @empty
            <p>Aucun créneau sur la période.</p>
        @endforelse
        <button class="btn btn-success mt-3">Enregistrer mon inscription</button>
    </div>
</form>
@endsection
