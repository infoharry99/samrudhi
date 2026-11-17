<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class MemberMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('error', 'Please login to access the member portal.');
        }

        if (!auth()->user()->isMember()) {
            return redirect()->route('admin.dashboard')->with('error', 'Admin redirected to administration portal.');
        }

        if (!auth()->user()->member_id) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'No member profile is linked to this account. Contact admin.');
        }

        return $next($request);
    }
}
