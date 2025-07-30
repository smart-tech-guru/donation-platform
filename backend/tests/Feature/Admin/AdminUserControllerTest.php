<?php

use App\Models\User;

test('admin can list users', function () {
    authenticateAdmin();
    User::factory()->count(5)->create();

    $response = $this->getJson('/api/admin/users');

    $response->assertStatus(200)
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

test('regular user cannot list users', function () {
    authenticateUser();

    $response = $this->getJson('/api/admin/users');

    $response->assertStatus(403);
});


