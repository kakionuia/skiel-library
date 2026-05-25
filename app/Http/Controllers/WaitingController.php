<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class WaitingController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        
        return Inertia::render('Auth/Waiting', [
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'status' => $user->status,
                'created_at' => $user->created_at,
            ]
        ]);
    }
}