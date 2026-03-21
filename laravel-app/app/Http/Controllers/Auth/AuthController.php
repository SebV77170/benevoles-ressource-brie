<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        $credentials = $request->safe()->only(['pseudo', 'password']);

        if (! Auth::attempt($credentials, (bool) $request->boolean('remember'))) {
            return back()->withErrors(['pseudo' => 'Identifiants invalides.'])->onlyInput('pseudo');
        }

        /** @var User $user */
        $user = $request->user();
        $user->profileDates()?->update(['date_derniere_visite' => now()]);
        $request->session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
