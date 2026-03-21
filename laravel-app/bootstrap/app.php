<?php

use Illuminate\Foundation\Application;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php'
    )
    ->withMiddleware(function ($middleware): void {
        $middleware->web(append: [
            App\Http\Middleware\EnsureVolunteerIsApproved::class,
        ]);
    })
    ->withProviders([
        App\Providers\AuthServiceProvider::class,
    ])
    ->create();
