<?php

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;

test('checkout validates campaign exists', function () {
    authenticateUser();

    $response = $this->postJson('/api/campaigns/999/checkout', [
        'amount' => 50
    ]);

    $response->assertStatus(404);
});

test('checkout validates amount is positive', function () {
    authenticateUser();
    $campaign = Campaign::factory()->create();

    $response = $this->postJson("/api/campaigns/{$campaign->id}/checkout", [
        'amount' => 0
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
});

test('checkout validates amount is numeric', function () {
    authenticateUser();
    $campaign = Campaign::factory()->create();

    $response = $this->postJson("/api/campaigns/{$campaign->id}/checkout", [
        'amount' => 'invalid'
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
});

test('confirm payment validates session id', function () {
    authenticateUser();
    $campaign = Campaign::factory()->create();

    $response = $this->postJson('/api/donations/confirm', [
        'campaign_id' => $campaign->id
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['session_id']);
});

test('confirm payment validates campaign exists', function () {
    authenticateUser();

    $response = $this->postJson('/api/donations/confirm', [
        'session_id' => 'test_session',
        'campaign_id' => 999
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['campaign_id']);
});

test('donation is created with correct data', function () {
    $user = authenticateUser();
    $campaign = Campaign::factory()->create();
    
    $donation = Donation::factory()->create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'amount' => 150
    ]);

    expect($donation->campaign_id)->toBe($campaign->id);
    expect($donation->user_id)->toBe($user->id);
    expect($donation->amount)->toBe('150.00');

    $this->assertDatabaseHas('donations', [
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'amount' => 150
    ]);
});

test('multiple donations to same campaign', function () {
    $user = authenticateUser();
    $campaign = Campaign::factory()->create();
    
    Donation::factory()->count(3)->create([
        'campaign_id' => $campaign->id,
        'user_id' => $user->id,
        'amount' => 100
    ]);

    expect($campaign->donations()->count())->toBe(3);
    expect($campaign->donations()->sum('amount'))->toBe(300);
});


