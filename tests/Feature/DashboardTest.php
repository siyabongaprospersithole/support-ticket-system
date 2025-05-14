<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that a guest is redirected to login when attempting to access dashboard.
     */
    public function test_dashboard_redirects_unauthenticated_user(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertRedirect(route('login'));
    }

    /**
     * Test authenticated users can access the dashboard.
     */
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page->component('Dashboard'));
    }

    /**
     * Test that a user must have verified email to access dashboard if verification is enabled.
     */
    public function test_email_verified_middleware_if_enabled(): void
    {
        // Skip this test if email verification is not enabled
        if (!config('auth.verify_email')) {
            $this->markTestSkipped('Email verification is not enabled.');
            return;
        }

        $user = User::factory()->create([
            'email_verified_at' => null
        ]);

        $response = $this->actingAs($user)
            ->get(route('dashboard'));

        $response->assertRedirect(route('verification.notice'));
    }
} 