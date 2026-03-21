<?php

namespace App\Policies;

use App\Models\EventSlot;
use App\Models\User;

class EventSlotPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isManager();
    }

    public function update(User $user, EventSlot $eventSlot): bool
    {
        return $user->isAdministrator();
    }

    public function delete(User $user, EventSlot $eventSlot): bool
    {
        return $user->isAdministrator();
    }
}
