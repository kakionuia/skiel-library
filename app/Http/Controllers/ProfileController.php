<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    private const POINT_PER_BOOK = 10;

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $user = Auth::user();
        
        $recentBorrows = $user->borrows()
            ->with('book')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        $activeBorrows = $user->borrows()
            ->with('book')
            ->whereIn('status', ['approved', 'borrowed'])
            ->orderBy('due_date', 'asc')
            ->get();

        $totalBorrows = $user->borrows()->count();
        
        $overdueCount = $user->borrows()
            ->whereIn('status', ['approved', 'borrowed'])
            ->where('due_date', '<', now())
            ->count();

        $totalFines = $user->borrows()
            ->where('status', '!=', 'returned')
            ->where('due_date', '<', now())
            ->sum('late_fee') ?? 0;

        $pointStatuses = ['approved', 'borrowed', 'returned'];
        $totalPoints = ((int) $user->borrows()
            ->whereIn('status', $pointStatuses)
            ->sum(DB::raw('COALESCE(quantity, 1)'))) * self::POINT_PER_BOOK;

        $monthlyPoints = ((int) $user->borrows()
            ->whereIn('status', $pointStatuses)
            ->whereBetween('borrow_date', [now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString()])
            ->sum(DB::raw('COALESCE(quantity, 1)'))) * self::POINT_PER_BOOK;

        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => $user,
            'recentBorrows' => $recentBorrows,
            'activeBorrows' => $activeBorrows,
            'totalBorrows' => $totalBorrows,
            'overdueCount' => $overdueCount,
            'totalFines' => (int)$totalFines,
            'totalPoints' => $totalPoints,
            'monthlyPoints' => $monthlyPoints,
            'pointPerBook' => self::POINT_PER_BOOK,
            'pointMonthLabel' => now()->translatedFormat('F Y'),
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

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Update the user's profile photo.
     */
    public function updateProfilePhoto(Request $request): RedirectResponse
    {
        $request->validate([
            'profile_photo' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old photo if exists
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        // Store new photo
        $path = $request->file('profile_photo')->store('profile-photos', 'public');
        
        $user->update([
            'profile_photo_path' => $path,
        ]);

        return Redirect::route('profile.edit')->with('status', 'profile-photo-updated');
    }

    /**
     * Delete the user's profile photo.
     */
    public function destroyProfilePhoto(Request $request): RedirectResponse
    {
        $user = $request->user();

        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            
            $user->update([
                'profile_photo_path' => null,
            ]);
        }

        return Redirect::route('profile.edit')->with('status', 'profile-photo-deleted');
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

        // Delete profile photo before deleting user
        if ($user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
