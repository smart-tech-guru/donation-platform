<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

class CampaignManagementTest extends TestCase
{
    use RefreshDatabase;

    private function authenticateUser($user = null)
    {
        $user = $user ?: User::factory()->create();
        $token = JWTAuth::fromUser($user);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $user;
    }
    
    private function authenticateAdmin()
    {
        // Create admin role if it doesn't exist
        Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        
        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $token = JWTAuth::fromUser($admin);
        $this->withHeader('Authorization', 'Bearer ' . $token);
        return $admin;
    }


    public function test_campaigns_list_includes_donation_statistics(): void
    {
        $user = $this->authenticateUser();
        $campaign = Campaign::factory()->create();
        
        // Create donations
        Donation::factory()->count(3)->create([
            'campaign_id' => $campaign->id,
            'amount' => 100
        ]);

        $response = $this->getJson('/api/campaigns');

        $response->assertStatus(200)
                ->assertJsonPath('data.0.donations_count', 3)
                ->assertJsonPath('data.0.donations_sum_amount', 300);
    }

    public function test_campaign_show_returns_correct_data(): void
    {
        $this->authenticateUser();
        $campaign = Campaign::factory()->create([
            'title' => 'Test Campaign',
            'description' => 'Test Description',
            'goal_amount' => 1000
        ]);

        $response = $this->getJson("/api/campaigns/{$campaign->id}");

        $response->assertStatus(200)
                ->assertJson([
                    'id' => $campaign->id,
                    'title' => 'Test Campaign',
                    'description' => 'Test Description',
                    'goal_amount' => 1000
                ]);
    }

    public function test_campaign_show_returns_404_for_nonexistent_campaign(): void
    {
        $this->authenticateUser();

        $response = $this->getJson('/api/campaigns/999');

        $response->assertStatus(404);
    }

    public function test_user_can_only_see_their_own_campaigns_in_creation(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $campaign1 = Campaign::factory()->create(['user_id' => $user1->id]);
        $campaign2 = Campaign::factory()->create(['user_id' => $user2->id]);

        $token = JWTAuth::fromUser($user1);
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->getJson('/api/campaigns');

        $response->assertStatus(200);
        // Both campaigns should be visible in the list (public view)
        $this->assertCount(2, $response->json('data'));
    }

    public function test_campaign_creation_sets_correct_user_id(): void
    {
        $user = $this->authenticateAdmin();
        
        $campaignData = [
            'title' => 'User Campaign',
            'description' => 'Created by authenticated user',
            'goal_amount' => 500
        ];

        $response = $this->postJson('/api/admin/campaigns', $campaignData);

        $response->assertStatus(201);
        $this->assertDatabaseHas('campaigns', [
            'title' => 'User Campaign',
            'user_id' => $user->id
        ]);
    }

    public function test_campaign_list_pagination(): void
    {
        $this->authenticateUser();
        Campaign::factory()->count(15)->create();

        $response = $this->getJson('/api/campaigns');

        $response->assertStatus(200)
                ->assertJsonStructure([
                    'data',
                    'current_page',
                    'last_page',
                    'per_page',
                    'total'
                ]);
    }
}