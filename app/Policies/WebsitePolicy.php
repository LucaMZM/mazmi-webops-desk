<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Website;

class WebsitePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Website $website): bool
    {
        return ! $user->isClient() || $user->client_id === $website->client_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Website $website): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Website $website): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Website $website): bool
    {
        return false;
    }

    public function forceDelete(User $user, Website $website): bool
    {
        return false;
    }
}
