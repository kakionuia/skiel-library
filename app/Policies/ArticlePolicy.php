<?php

namespace App\Policies;

use App\Models\Article;
use App\Models\User;

class ArticlePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Anyone can view published articles
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(?User $user, Article $article): bool
    {
        // Anyone can view approved articles
        if ($article->status === 'approved') {
            return true;
        }

        // Only author and admin can view draft/pending/rejected
        return $user && ($user->id === $article->author_id || $user->is_admin);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Admin or petugas can create
        return $user->is_admin || $user->role === 'petugas';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Article $article): bool
    {
        // Admin can update any article
        if ($user->is_admin) {
            return true;
        }

        // Petugas can only update their own articles
        return $user->id === $article->author_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Article $article): bool
    {
        // Admin can delete any article
        if ($user->is_admin) {
            return true;
        }

        // Petugas can only delete their own draft/pending/rejected articles
        return $user->id === $article->author_id && $article->status !== 'approved';
    }

    /**
     * Determine if user is admin (for admin panel access)
     */
    public function admin(User $user): bool
    {
        return $user->is_admin;
    }
}
