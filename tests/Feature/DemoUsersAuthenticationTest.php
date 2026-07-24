<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DemoUsersAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_demo_user_can_log_in_with_the_documented_password(): void
    {
        $this->seed();

        $emails = [
            'admin@webops.test',
            'tech1@webops.test',
            'tech2@webops.test',
            'cliente1@webops.test',
            'cliente2@webops.test',
            'cliente3@webops.test',
        ];

        foreach ($emails as $email) {
            $response = $this->post('/login', ['email' => $email, 'password' => 'password']);

            $response->assertRedirect(route('dashboard', absolute: false));
            $this->assertAuthenticatedAs(User::where('email', $email)->firstOrFail());
            $this->post('/logout')->assertRedirect('/');
            $this->assertGuest();
        }
    }
}
