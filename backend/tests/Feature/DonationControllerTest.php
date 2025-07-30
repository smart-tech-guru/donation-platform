<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class DonationControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser($user = null)
    {
        $user = $user ?: User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $user;
    }

    public function test_can_create_checkout_session(): void
    {
        $this->authenticateUser();
        $campaign = Campaign::factory()->create();

        // Mock Stripe (you'll need to set up proper mocking)
        $response = $this->postJson("/api/campaigns/{$campaign->id}/checkout", [
            'amount' => 50
        ]);

        // This will fail without proper Stripe mocking, but shows the structure
        $response->assertStatus(200)
                ->assertJsonStructure(['sessionId']);
    }

    public function test_checkout_requires_authentication(): void
    {
        $campaign = Campaign::factory()->create();

        $response = $this->postJson("/api/campaigns/{$campaign->id}/checkout", [
            'amount' => 50
        ]);

        $response->assertStatus(401);
    }

    public function test_checkout_validates_amount(): void
    {
        $this->authenticateUser();
        $campaign = Campaign::factory()->create();

        $response = $this->postJson("/api/campaigns/{$campaign->id}/checkout", [
            'amount' => -10
        ]);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['amount']);
    }

    public function test_confirm_payment_requires_authentication(): void
    {
        $response = $this->postJson('/api/donations/confirm', [
            'session_id' => 'test_session',
            'campaign_id' => 1
        ]);

        $response->assertStatus(401);
    }

    public function test_confirm_payment_validates_required_fields(): void
    {
        $this->authenticateUser();

        $response = $this->postJson('/api/donations/confirm', []);

        $response->assertStatus(422)
                ->assertJsonValidationErrors(['session_id', 'campaign_id']);
    }
}