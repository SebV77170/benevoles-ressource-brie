<?php

namespace App\Providers;

use App\Models\EventSlot;
use App\Policies\EventSlotPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        EventSlot::class => EventSlotPolicy::class,
    ];

    public function boot(): void
    {
        // Policies auto-registered.
    }
}
