<?php

namespace App\Policies;

use App\Models\BookQueue;
use App\Models\User;

class BookQueuePolicy
{
    /**
     * Determine whether the user can delete the queue entry.
     */
    public function delete(User $user, BookQueue $queue): bool
    {
        return $user->id === $queue->user_id;
    }

    /**
     * Determine whether the user can view the queue entry.
     */
    public function view(User $user, BookQueue $queue): bool
    {
        return $user->id === $queue->user_id;
    }
}
