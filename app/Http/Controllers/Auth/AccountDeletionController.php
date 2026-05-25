<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AccountDeletionController extends Controller
{
    /**
     * Delete the user's account
     */
    public function delete(Request $request): RedirectResponse
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'User tidak ditemukan.');
        }

        // Only allow deletion for pending or rejected users
        if ($user->status !== 'pending' && $user->status !== 'rejected') {
            return redirect()->route('waiting')->with('error', 'Akun tidak dapat dihapus.');
        }

        // Log out the user
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        // Delete the user from database
        DB::table('users')->where('id', $user->id)->delete();

        return redirect('/')->with('success', 'Akun Anda telah berhasil dihapus.');
    }
}
