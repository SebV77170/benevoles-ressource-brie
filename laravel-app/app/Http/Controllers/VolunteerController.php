<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfileRequest;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class VolunteerController extends Controller
{
    public function index(): View
    {
        $volunteers = User::query()->with('profileDates')->orderBy('nom')->orderBy('prenom')->get();

        return view('volunteers.index', compact('volunteers'));
    }

    public function profile(): View
    {
        $user = auth()->user()->load(['profileDates', 'eventSlots']);

        return view('volunteers.profile', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $request->user()->update($request->safe()->only(['mail', 'tel']));

        return back()->with('status', 'Profil mis à jour.');
    }
}
