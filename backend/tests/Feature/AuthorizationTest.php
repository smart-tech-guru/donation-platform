<?php

use App\Models\Campaign;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;
use Spatie\Permission\Models\Role;

test('unauthenticated user cannot access protected routes', function () {
    $routes = [
        ['GET', '/api/campaigns'],
        ['GET', '/api/user'],
        ['POST', '/api/logout'],
        ['GET', '/api/admin/campaigns'],
        ['GET', '/api/admin/users'],
    ];

    foreach ($routes as [$method, $route]) {
        $response = $this->json($method, $route);
        $response->assertStatus(401);
    }
});

test('regular user cannot access admin routes', function () {
    $user = User::factory()->create();
    $token = JWTAuth::fromUser($user);
    
    $campaign = Campaign::factory()->create();

    $adminRoutes = [
        ['GET', '/api/admin/campaigns'],
        ['GET', '/api/admin/users'],
        ['DELETE', "/api/admin/campaigns/{$campaign->id}"],
    ];

    foreach ($adminRoutes as [$method, $route]) {
        $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                        ->json($method, $route);
        $response->assertStatus(403);
    }
});

test('admin can access admin routes', function () {
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $token = JWTAuth::fromUser($admin);

    $response = $this->withHeader('Authorization', 'Bearer ' . $token)
                    ->getJson('/api/admin/campaigns');

    $response->assertStatus(200);
});

test('invalid token returns unauthorized', function () {
    $response = $this->withHeader('Authorization', 'Bearer invalid-token')
                    ->getJson('/api/campaigns');

    $response->assertStatus(401);
});

