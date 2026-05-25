<?php
// app/Http/Middleware/CheckBorrowPermission.php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckBorrowPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Cek apakah user adalah petugas atau admin
        if ($user && ($user->role === 'petugas' || $user->role === 'admin')) {
            return $next($request);
        }

        abort(403, 'Unauthorized. Only librarians and admins can perform this action.');
    }
}