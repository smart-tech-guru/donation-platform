<?php

use App\Models\Campaign;
use App\Models\User;

test('campaign title is required', function () {
    authenticateAdmin();

    $response = $this->postJson('/api/admin/campaigns', [
        'description' => 'Test Description',
        'goal_amount' => 1000,
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
});

test('campaign goal amount must be positive', function () {
    authenticateAdmin();

    $response = $this->postJson('/api/admin/campaigns', [
        'title' => 'Test Campaign',
        'description' => 'Test Description',
        'goal_amount' => -100,
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['goal_amount']);
});

test('donation amount validation', function () {
    authenticateUser();
    $campaign = Campaign::factory()->create();

    $response = $this->postJson("/api/campaigns/{$campaign->id}/checkout", [
        'amount' => 0
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
});

test('donation amount must be numeric', function () {
    authenticateUser();
    $campaign = Campaign::factory()->create();

    $response = $this->postJson("/api/campaigns/{$campaign->id}/checkout", [
        'amount' => 'not-a-number'
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['amount']);
});

test('registration email must be unique', function () {
    User::factory()->create(['email' => 'test@example.com']);

    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123'
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
});

test('registration password confirmation required', function () {
    $response = $this->postJson('/api/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
});

test('login email is required', function () {
    $response = $this->postJson('/api/login', [
        'password' => 'password123'
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
});

test('login password is required', function () {
    $response = $this->postJson('/api/login', [
        'email' => 'test@example.com'
    ]);

    $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
});

