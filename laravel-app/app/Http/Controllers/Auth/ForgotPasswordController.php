<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForgotPasswordLinkRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Password;

class ForgotPasswordController extends Controller
{
    public function create()
    {
        return view('auth.forgot-password');
    }

    public function store(ForgotPasswordLinkRequest $request): RedirectResponse
    {
        $user = User::query()
            ->where('pseudo', $request->string('pseudo'))
            ->where('mail', $request->string('mail'))
            ->first();

        if (! $user) {
            return back()->withErrors(['mail' => 'Aucun compte ne correspond à ces informations.']);
        }

        $status = Password::broker()->sendResetLink(['mail' => $user->mail]);

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['mail' => __($status)]);
    }
}
