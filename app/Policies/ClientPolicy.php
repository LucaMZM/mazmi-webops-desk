<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Client $client): bool
    {
        return ! $user->isClient() || $user->client_id === $client->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, Client $client): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, Client $client): bool
    {
        return false;
    }

    public function forceDelete(User $user, Client $client): bool
    {
        return false;
    }
}
