<?php

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;

test('admin can list campaigns', function () {
    authenticateAdmin();
    Campaign::factory()->count(3)->create();

    $response = $this->getJson('/api/admin/campaigns');

    $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'description',
                        'goal_amount'
                    ]
                ]
            ]);
});

test('regular user cannot access admin campaigns', function () {
    authenticateUser();

    $response = $this->getJson('/api/admin/campaigns');

    $response->assertStatus(403);
});

test('admin can view campaign with donations', function () {
    authenticateAdmin();
    $campaign = Campaign::factory()->create();
    $user = User::factory()->create();
    
    Donation::factory()->create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'amount' => 100
    ]);

    $response = $this->getJson("/api/admin/campaigns/{$campaign->id}");

    $response->assertStatus(200)
            ->assertJsonStructure([
                'campaign' => [
                    'id',
                    'title',
                    'description',
                    'goal_amount',
                    'donations_count',
                    'donations_sum_amount'
                ],
                'donations' => [
                    '*' => [
                        'id',
                        'amount',
                        'created_at',
                        'user' => [
                            'id',
                            'name',
                            'email'
                        ]
                    ]
                ]
            ]);
});

test('admin can delete campaign', function () {
    authenticateAdmin();
    $campaign = Campaign::factory()->create();

    $response = $this->deleteJson("/api/admin/campaigns/{$campaign->id}");

    $response->assertStatus(204);
    $this->assertDatabaseMissing('campaigns', ['id' => $campaign->id]);
});

test('regular user cannot delete campaign', function () {
    authenticateUser();
    $campaign = Campaign::factory()->create();

    $response = $this->deleteJson("/api/admin/campaigns/{$campaign->id}");

    $response->assertStatus(403);
    $this->assertDatabaseHas('campaigns', ['id' => $campaign->id]);
});


