<?php

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;

test('campaign fillable attributes', function () {
    $campaign = new Campaign();
    $fillable = ['title', 'description', 'goal_amount', 'user_id'];
    
    expect($campaign->getFillable())->toEqualCanonicalizing($fillable);
});

test('campaign has donations relationship method', function () {
    $campaign = new Campaign();
    
    expect($campaign->donations())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\HasMany::class);
});

test('campaign has user relationship method', function () {
    $campaign = new Campaign();
    
    expect($campaign->user())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('campaign casts attributes correctly', function () {
    $campaign = new Campaign();
    
    expect($campaign->getCasts())->toHaveKey('goal_amount');
});


