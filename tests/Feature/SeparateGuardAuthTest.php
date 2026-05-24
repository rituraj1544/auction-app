<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeparateGuardAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_and_admin_guards_can_be_authenticated_in_the_same_session(): void
    {
        $user = User::factory()->create(['role' => 'user']);
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user, 'web');
        $this->actingAs($admin, 'admin');

        $this->get('/user/dashboard')->assertOk();
        $this->get('/admin/dashboard')->assertOk();
    }

    public function test_user_guard_alone_does_not_open_admin_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $this->actingAs($user, 'web')
            ->get('/admin/dashboard')
            ->assertRedirect(route('admin.login'));
    }
}
