<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsPending
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        $user = $request->user();

        // If user is approved, redirect to dashboard
        if ($user->status === 'approved') {
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard');
            } elseif ($user->role === 'petugas') {
                return redirect()->route('petugas.dashboard');
            } else {
                return redirect()->route('dashboard');
            }
        }

        // Allow both pending and rejected users to access waiting page
        if ($user->status === 'pending' || $user->status === 'rejected') {
            return $next($request);
        }

        // Any other status should redirect to login
        return redirect()->route('login');

        return $next($request);
    }
}
