<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    // Di controller ProfileController
public function edit(Request $request)
    {
        $user = $request->user();

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'recentActivities' => $user->borrows()->latest()->take(5)->get()->map(fn($b) => [
    'id'          => $b->id,
    'description' => 'Meminjam buku "'.$b->book->judul.'"',
    'created_at'  => $b->created_at->format('d M Y'),
]),
            'recentBooks' => $user->peminjaman()  // sekarang method ini ada
                                   ->with('buku') // asumsikan Peminjaman punya relasi 'buku'
                                   ->latest()
                                   ->take(3)
                                   ->get()
                                   ->map(fn($p) => [
                                       'id' => $p->id,
                                       'judul' => $p->buku->judul ?? 'Tanpa Judul',
                                       'tanggal_baca' => $p->created_at->format('d M Y'),
                                   ]),
        ]);
    }
    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
