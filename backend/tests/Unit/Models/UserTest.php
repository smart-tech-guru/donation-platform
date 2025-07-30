<?php

use App\Models\User;

test('user fillable attributes', function () {
    $user = new User();
    $expected = ['name', 'email', 'password'];
    
    expect($user->getFillable())->toBe($expected);
});

test('user hidden attributes', function () {
    $user = new User();
    $expected = ['password', 'remember_token'];
    
    expect($user->getHidden())->toBe($expected);
});

test('user casts', function () {
    $user = new User();
    $expected = [
        'id' => 'int',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    
    expect($user->getCasts())->toBe($expected);
});

test('user has campaigns relationship method', function () {
    $user = new User();
    
    expect($user->campaigns())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('user has donations relationship method', function () {
    $user = new User();
    
    expect($user->donations())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});


