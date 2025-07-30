<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;

class CampaignControllerTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser($user = null)
    {
        $user = $user ?: User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $user;
    }

    public function test_can_list_campaigns(): void
    {
        $this->authenticateUser();
        Campaign::factory()->count(3)->create();

        $response = $this->getJson('/api/campaigns');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data' => [
                        '*' => [
                            'id',
                            'title',
                            'description',
                            'goal_amount',
                            'donations_count',
                            'donations_sum_amount',
                        ]
                    ]
                ]);
    }

    public function test_can_show_single_campaign(): void
    {
        $this->authenticateUser();
        $campaign = Campaign::factory()->create();

        $response = $this->getJson("/api/campaigns/{$campaign->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $campaign->id,
                    'title' => $campaign->title,
                    'description' => $campaign->description,
                    'goal_amount' => $campaign->goal_amount,
                ]);
    }

}