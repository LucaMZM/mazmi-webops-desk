<?php

namespace App\Policies;

use App\Models\MaintenanceTask;
use App\Models\User;

class MaintenanceTaskPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MaintenanceTask $maintenanceTask): bool
    {
        return ! $user->isClient() || $user->client_id === $maintenanceTask->website->client_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MaintenanceTask $maintenanceTask): bool
    {
        return $user->isAdmin() || ($user->isTechnician() && $maintenanceTask->assigned_to === $user->id);
    }

    public function delete(User $user, MaintenanceTask $maintenanceTask): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, MaintenanceTask $maintenanceTask): bool
    {
        return false;
    }

    public function forceDelete(User $user, MaintenanceTask $maintenanceTask): bool
    {
        return false;
    }
}
