<?php

namespace App\Http\Controllers;

use App\Models\LiteracyEvent;
use App\Models\LiteracyEventParticipant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class LiteracyEventController extends Controller
{
    private const POINT_PER_BOOK = 10;

    public function adminIndex()
    {
        $events = LiteracyEvent::with(['winner:id,name,profile_photo_path'])
            ->withCount('participants')
            ->latest()
            ->get();

        return Inertia::render('Admin/Events/Index', [
            'events' => $events,
            'leaderboard' => $this->globalLeaderboard(),
            'pointPerBook' => self::POINT_PER_BOOK,
        ]);
    }

    public function adminStore(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'terms' => 'nullable|string',
            'category' => 'required|in:point,karya',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        }

        unset($validated['image']);

        LiteracyEvent::create([
            ...$validated,
            'slug' => LiteracyEvent::generateSlug($validated['title']),
            'status' => 'active',
            'created_by' => Auth::id(),
        ]);

        return back()->with('success', 'Event literasi berhasil dibuat.');
    }

    public function adminShow(LiteracyEvent $literacyEvent)
    {
        $literacyEvent->load(['winner:id,name,profile_photo_path']);

        return Inertia::render('Admin/Events/Show', [
            'event' => $literacyEvent,
            'participants' => $this->eventParticipants($literacyEvent),
            'pointPerBook' => self::POINT_PER_BOOK,
        ]);
    }

    public function adminUpdate(Request $request, LiteracyEvent $literacyEvent)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'terms' => 'nullable|string',
            'category' => 'required|in:point,karya',
            'starts_at' => 'required|date',
            'ends_at' => 'required|date|after_or_equal:starts_at',
            'image' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        if ($validated['title'] !== $literacyEvent->title) {
            $validated['slug'] = LiteracyEvent::generateSlug($validated['title']);
        }

        if ($request->hasFile('image')) {
            if ($literacyEvent->image_path) {
                Storage::disk('public')->delete($literacyEvent->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('events', 'public');
        }

        unset($validated['image']);
        $literacyEvent->update($validated);

        return back()->with('success', 'Event literasi berhasil diperbarui.');
    }

    public function adminDestroy(LiteracyEvent $literacyEvent)
    {
        if ($literacyEvent->image_path) {
            Storage::disk('public')->delete($literacyEvent->image_path);
        }

        $literacyEvent->participants()
            ->whereNotNull('submission_path')
            ->pluck('submission_path')
            ->each(fn ($path) => Storage::disk('public')->delete($path));

        $literacyEvent->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event berhasil dicabut.');
    }

    public function chooseWinner(LiteracyEvent $literacyEvent, User $user)
    {
        $participant = $literacyEvent->participants()->where('user_id', $user->id)->firstOrFail();

        DB::transaction(function () use ($literacyEvent, $participant, $user) {
            $literacyEvent->participants()->update(['is_winner' => false]);
            $participant->update(['is_winner' => true]);
            $literacyEvent->update([
                'winner_user_id' => $user->id,
                'status' => 'ended',
                'ended_at' => now(),
            ]);
        });

        return back()->with('success', 'Pemenang event berhasil ditentukan.');
    }

    public function viewSubmission(LiteracyEvent $literacyEvent, LiteracyEventParticipant $participant)
    {
        abort_unless($participant->literacy_event_id === $literacyEvent->id, 404);
        abort_unless($participant->submission_path && Storage::disk('public')->exists($participant->submission_path), 404);

        return response()->file(Storage::disk('public')->path($participant->submission_path));
    }

    public function show(LiteracyEvent $literacyEvent)
    {
        $literacyEvent->load(['winner:id,name,profile_photo_path']);

        $participation = null;
        if (Auth::check()) {
            $participation = $literacyEvent->participants()
                ->where('user_id', Auth::id())
                ->first();
        }

        return Inertia::render('Events/Show', [
            'event' => $literacyEvent,
            'participation' => $participation,
            'userEventPoints' => Auth::check() ? $this->pointsForUserInEvent(Auth::id(), $literacyEvent) : 0,
            'authRequired' => !Auth::check(),
        ]);
    }

    public function participate(Request $request, LiteracyEvent $literacyEvent)
    {
        abort_unless($literacyEvent->is_running, 422, 'Event sudah tidak menerima partisipasi.');

        $rules = [];
        if ($literacyEvent->category === 'karya') {
            $rules['submission'] = 'required|file|mimes:pdf|max:10240';
        }

        $validated = $request->validate($rules);

        $existing = $literacyEvent->participants()
            ->where('user_id', Auth::id())
            ->first();

        if ($existing) {
            return back()->withErrors(['submission' => 'Anda sudah berpartisipasi di event ini.']);
        }

        $data = [
            'literacy_event_id' => $literacyEvent->id,
            'user_id' => Auth::id(),
        ];

        if ($literacyEvent->category === 'karya') {
            $data['submission_path'] = $request->file('submission')->store('event-submissions', 'public');
            $data['submitted_at'] = now();
        }

        LiteracyEventParticipant::create($data);

        return back()->with('success', 'Partisipasi event berhasil dikirim.');
    }

    private function globalLeaderboard()
    {
        return User::query()
            ->where('role', 'anggota')
            ->leftJoin('borrows', function ($join) {
                $join->on('users.id', '=', 'borrows.user_id')
                    ->whereIn('borrows.status', ['approved', 'borrowed', 'returned']);
            })
            ->select('users.id', 'users.name', 'users.email', 'users.profile_photo_path')
            ->selectRaw('COALESCE(SUM(COALESCE(borrows.quantity, 1)), 0) * ? as points', [self::POINT_PER_BOOK])
            ->groupBy('users.id', 'users.name', 'users.email', 'users.profile_photo_path')
            ->orderByDesc('points')
            ->limit(50)
            ->get()
            ->map(fn ($user) => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo_url' => $user->profile_photo_path ? '/storage/' . $user->profile_photo_path : null,
                'points' => (int) $user->points,
            ]);
    }

    private function eventParticipants(LiteracyEvent $event)
    {
        $participants = $event->participants()
            ->with('user:id,name,email,profile_photo_path')
            ->latest()
            ->get()
            ->map(function (LiteracyEventParticipant $participant) use ($event) {
                $participant->event_points = $this->pointsForUserInEvent($participant->user_id, $event);
                return $participant;
            });

        if ($event->category === 'point') {
            return $participants->sortByDesc('event_points')->values();
        }

        return $participants->values();
    }

    private function pointsForUserInEvent(int $userId, LiteracyEvent $event): int
    {
        $bookCount = DB::table('borrows')
            ->where('user_id', $userId)
            ->whereIn('status', ['approved', 'borrowed', 'returned'])
            ->whereDate('borrow_date', '>=', $event->starts_at)
            ->whereDate('borrow_date', '<=', $event->ends_at)
            ->sum(DB::raw('COALESCE(quantity, 1)'));

        return (int) $bookCount * self::POINT_PER_BOOK;
    }
}
