<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LegacyPasswordMigrationService
{
    public function ensurePasswordCanBeReset(User $user): void
    {
        if ($this->looksLikeBcrypt($user->password)) {
            return;
        }

        $user->forceFill([
            'password' => Hash::make(str()->random(40)),
        ])->save();
    }

    public function looksLikeBcrypt(?string $hash): bool
    {
        return is_string($hash) && str_starts_with($hash, '$2y$');
    }
}
