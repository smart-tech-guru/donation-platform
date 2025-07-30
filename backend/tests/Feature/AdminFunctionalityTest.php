<?php

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;

beforeEach(function () {
    $this->seed(\Database\Seeders\RoleSeeder::class);
});

test('admin campaign view includes donation details', function () {
    authenticateAdmin();
    $campaign = Campaign::factory()->create();
    $user = User::factory()->create(['name' => 'John Doe', 'email' => 'john@example.com']);
    
    $donation = Donation::factory()->create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'amount' => 250
    ]);

    $response = $this->getJson("/api/admin/campaigns/{$campaign->id}");

    $response->assertStatus(200)
            ->assertJsonPath('donations.0.amount', '250.00')
            ->assertJsonPath('donations.0.user.name', 'John Doe')
            ->assertJsonPath('donations.0.user.email', 'john@example.com');
});

test('admin can view campaign without donations', function () {
    authenticateAdmin();
    $campaign = Campaign::factory()->create();

    $response = $this->getJson("/api/admin/campaigns/{$campaign->id}");

    $response->assertStatus(200)
            ->assertJsonPath('campaign.donations_count', 0)
            ->assertJsonPath('campaign.donations_sum_amount', null)
            ->assertJsonPath('donations', []);
});

test('admin campaign view returns 404 for nonexistent', function () {
    authenticateAdmin();

    $response = $this->getJson('/api/admin/campaigns/999');

    $response->assertStatus(404);
});

test('admin can delete campaign with donations', function () {
    authenticateAdmin();
    $campaign = Campaign::factory()->create();
    
    Donation::factory()->create(['campaign_id' => $campaign->id]);

    $response = $this->deleteJson("/api/admin/campaigns/{$campaign->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
});

test('admin delete returns 404 for nonexistent campaign', function () {
    authenticateAdmin();

    $response = $this->deleteJson('/api/admin/campaigns/999');

    $response->assertStatus(404);
});

test('admin users list excludes sensitive data', function () {
    authenticateAdmin();
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->getJson('/api/admin/users');

    $response->assertStatus(200)
            ->assertJsonMissing(['password'])
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name', 
                        'email',
                        'created_at'
                    ]
                ]
            ]);
});

test('admin users list pagination', function () {
    authenticateAdmin();
    User::factory()->count(15)->create();

    $response = $this->getJson('/api/admin/users');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data',
                'current_page',
                'last_page',
                'per_page',
                'total'
            ]);
});



