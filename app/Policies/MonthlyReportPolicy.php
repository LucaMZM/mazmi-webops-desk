<?php

namespace App\Policies;

use App\Models\MonthlyReport;
use App\Models\User;

class MonthlyReportPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MonthlyReport $monthlyReport): bool
    {
        return ! $user->isClient() || $user->client_id === $monthlyReport->client_id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, MonthlyReport $monthlyReport): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, MonthlyReport $monthlyReport): bool
    {
        return $user->isAdmin();
    }

    public function restore(User $user, MonthlyReport $monthlyReport): bool
    {
        return false;
    }

    public function forceDelete(User $user, MonthlyReport $monthlyReport): bool
    {
        return false;
    }
}
