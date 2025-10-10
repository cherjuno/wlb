<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user)
    {
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model)
    {
        // Admin bisa melihat semua user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Manager bisa melihat subordinates
        if ($user->hasRole('manager')) {
            return $user->subordinates()->where('id', $model->id)->exists() || $user->id === $model->id;
        }

        // Employee hanya bisa melihat diri sendiri
        return $user->id === $model->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user)
    {
        return $user->hasRole(['admin', 'manager']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model)
    {
        // Admin bisa update semua user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Manager bisa update subordinates
        if ($user->hasRole('manager')) {
            return $user->subordinates()->where('id', $model->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model)
    {
        // Tidak bisa menghapus diri sendiri
        if ($user->id === $model->id) {
            return false;
        }

        // Admin bisa delete semua user
        if ($user->hasRole('admin')) {
            return true;
        }

        // Manager bisa delete subordinates (kecuali admin)
        if ($user->hasRole('manager')) {
            return $user->subordinates()->where('id', $model->id)->exists() && !$model->hasRole('admin');
        }

        return false;
    }
}