<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
{
    $request->validate([
        'name' => 'required|string|max:255',
        'nisn' => 'required|string|max:20|unique:users,nisn',   // <-- validasi NISN
        'phone' => 'required|string|max:20',
        'email' => 'required|string|lowercase|email|max:255|unique:'.User::class,
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'kelas' => 'required|string|in:10,11,12',                // <-- validasi kelas
        'jurusan' => 'required|string|in:Elektro 1,Elektro 2,Elektro 3,Elektro 4,Mesin 1,Mesin 2,Mesin 3,Kimia 1,Kimia 2,Kimia 3,Las 1,Las 2,Las 3,RPL 1,RPL 2,RPL 3,RPL 4',
    ]);

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'nisn' => $request->nisn,
        'phone' => $request->phone,
        'kelas' => $request->kelas,
        'jurusan' => $request->jurusan,
        'role' => 'anggota',
        'status' => 'pending',
    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('waiting'));
}
}
