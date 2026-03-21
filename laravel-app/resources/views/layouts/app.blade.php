<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Ressource Brie' }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg navbar-dark bg-success mb-4">
    <div class="container">
        <a class="navbar-brand" href="{{ route('dashboard') }}">Ressource Brie</a>
        @auth
            <div class="d-flex gap-2 align-items-center text-white">
                <a class="btn btn-outline-light btn-sm" href="{{ route('planning.index') }}">Planning</a>
                <a class="btn btn-outline-light btn-sm" href="{{ route('volunteers.profile') }}">Profil</a>
                @if(auth()->user()->isManager())
                    <a class="btn btn-outline-light btn-sm" href="{{ route('volunteers.index') }}">Bénévoles</a>
                    <a class="btn btn-outline-light btn-sm" href="{{ route('admin.planning.index') }}">Admin</a>
                @endif
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-light btn-sm">Déconnexion</button>
                </form>
            </div>
        @endauth
    </div>
</nav>
<main class="container pb-5">
    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @yield('content')
</main>
</body>
</html>
