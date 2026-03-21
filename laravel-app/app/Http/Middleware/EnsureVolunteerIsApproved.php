<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureVolunteerIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return $next($request);
        }

        if ($request->user()->isPending() && ! $request->routeIs('pending.notice', 'logout')) {
            return redirect()->route('pending.notice');
        }

        return $next($request);
    }
}
