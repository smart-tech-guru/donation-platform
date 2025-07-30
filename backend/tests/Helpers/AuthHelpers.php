<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Tymon\JWTAuth\Facades\JWTAuth;

function authenticateAdmin()
{
    Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
    
    $admin = User::factory()->create();
    $admin->assignRole('admin');
    $token = JWTAuth::fromUser($admin);
    test()->withHeader('Authorization', 'Bearer ' . $token);
    return $admin;
}

function authenticateUser($user = null)
{
    $user = $user ?: User::factory()->create();
    $token = JWTAuth::fromUser($user);
    test()->withHeader('Authorization', 'Bearer ' . $token);
    return $user;
}