<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AccountController extends Controller
{
    public function index()
    {
        $admins = User::where('role', 'admin')->get();
        $petugas = User::where('role', 'petugas')->get();
        $users = User::where('role', 'anggota')->get();

        return inertia('Admin/Accounts/Index', [
            'admins' => $admins,
            'petugas' => $petugas,
            'users' => $users,
        ]);
    }

    public function storePetugas(Request $request)
    {
        $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|max:255',
        ]);

        $password = Str::random(8);

        $petugas = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($password),
            'role' => 'petugas',
            'status' => 'approved',
        ]);

        return back()->with('success', 'Akun petugas berhasil dibuat. Password: ' . $password);
    }

    public function approveUser(User $user)
    {
        if ($user->role !== 'anggota') {
            return back()->with('error', 'Hanya akun anggota yang dapat disetujui.');
        }

        $user->update(['status' => 'approved']);

        return back()->with('success', 'Akun anggota telah disetujui.');
    }

    public function rejectUser(User $user)
    {
        if ($user->role !== 'anggota') {
            return back()->with('error', 'Hanya akun anggota yang dapat ditolak.');
        }

        $user->update(['status' => 'rejected']);

        return back()->with('success', 'Akun anggota telah ditolak.');
    }

    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak dapat menghapus akun admin.');
        }

        $user->delete();

        return back()->with('success', 'Akun berhasil dihapus.');
    }
}