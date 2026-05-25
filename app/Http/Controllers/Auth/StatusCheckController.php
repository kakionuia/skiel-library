<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StatusCheckController extends Controller
{
    /**
     * Check current user's approval status
     */
    public function checkApprovalStatus(Request $request): JsonResponse
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'status' => 'unauthenticated',
                'message' => 'User not authenticated'
            ], 401);
        }

        return response()->json([
            'status' => $user->status,
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }
}
