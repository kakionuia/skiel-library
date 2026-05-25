<?php

namespace App\Http\Controllers;

use App\Models\Complaint;
use App\Models\Suggestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class FeedbackController extends Controller
{
    /**
     * Display feedback form
     */
    public function show()
    {
        return Inertia::render('FeedbackForm');
    }

    /**
     * Store feedback (complaint or suggestion)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'feedback_type' => 'required|in:suggestion,complaint',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        // Get user ID if authenticated, otherwise use null
        $userId = Auth::id();

        if ($validated['feedback_type'] === 'suggestion') {
            Suggestion::create([
                'user_id' => $userId,
                'content' => $validated['message'],
                'status' => 'pending',
            ]);
        } else {
            Complaint::create([
                'user_id' => $userId,
                'subject' => $validated['subject'],
                'description' => $validated['message'],
                'status' => 'pending',
            ]);
        }

        // Return redirect to home page
        return redirect(route('home'))->with('success', 'Feedback Anda telah terkirim. Terima kasih!');
    }
}
