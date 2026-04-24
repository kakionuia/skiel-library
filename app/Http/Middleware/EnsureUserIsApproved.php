<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsApproved
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user()) {
            return redirect()->route('login');
        }

        // If user is not approved, redirect to waiting page
        if ($request->user()->status === 'pending') {
            return redirect()->route('waiting');
        }

        if ($request->user()->status === 'rejected') {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda ditolak. Silakan hubungi admin.');
        }

        // Only allow user role that is approved
        if ($request->user()->role !== 'anggota' || $request->user()->status !== 'approved') {
            abort(403, 'Akses tidak diizinkan.');
        }

        return $next($request);
    }
}