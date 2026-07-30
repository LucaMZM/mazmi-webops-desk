<?php

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserRoleTest extends TestCase
{
    public function test_admin_role_helpers_are_consistent(): void
    {
        $user = new User(['role' => 'admin']);

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isTechnician());
        $this->assertFalse($user->isClient());
    }

    public function test_technician_role_helpers_are_consistent(): void
    {
        $user = new User(['role' => 'technician']);

        $this->assertFalse($user->isAdmin());
        $this->assertTrue($user->isTechnician());
        $this->assertFalse($user->isClient());
    }

    public function test_client_role_helpers_are_consistent(): void
    {
        $user = new User(['role' => 'client']);

        $this->assertFalse($user->isAdmin());
        $this->assertFalse($user->isTechnician());
        $this->assertTrue($user->isClient());
    }
}
