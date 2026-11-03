<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsurePlanner
{
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user()?->role !== 'planner') {
            return redirect()->back()->withErrors([
                'authorization' => 'Alleen planners kunnen diensten en taken beheren.',
            ]);
        }

        return $next($request);
    }
}
