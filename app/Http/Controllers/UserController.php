<?php

namespace App\Http\Controllers;

use App\Models\User; // Mengambil model User dari database
use Illuminate\Http\Request;

class UserController extends Controller
{
    // Fungsi untuk mengambil semua data user dalam bentuk JSON
    public function index()
    {
        $users = User::all(); 
        return response()->json($users);
    }
}