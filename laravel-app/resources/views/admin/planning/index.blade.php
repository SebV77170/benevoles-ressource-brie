@extends('layouts.app', ['title' => 'Administration planning'])

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <h1 class="h2">Administration du planning</h1>
        <table class="table table-striped align-middle">
            <thead><tr><th>ID</th><th>Créneau</th><th>Type</th><th>Bénévoles</th></tr></thead>
            <tbody>
            @foreach($slots as $slot)
                <tr>
                    <td>{{ $slot->id }}</td>
                    <td>{{ $slot->start?->format('d/m/Y H:i') }} → {{ $slot->end?->format('H:i') }}</td>
                    <td>{{ $slot->cat_creneau === 0 ? 'Global' : 'Sous-créneau' }}</td>
                    <td>{{ $slot->volunteers_count }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        {{ $slots->links() }}
    </div>
</div>
@endsection
