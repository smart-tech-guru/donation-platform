<?php

use App\Models\Donation;

test('donation fillable attributes', function () {
    $donation = new Donation();
    $fillable = ['campaign_id', 'user_id', 'amount'];
    
    expect($donation->getFillable())->toBe($fillable);
});

test('donation belongs to campaign relationship method', function () {
    $donation = new Donation();
    
    expect($donation->campaign())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('donation belongs to user relationship method', function () {
    $donation = new Donation();
    
    expect($donation->user())->toBeInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsTo::class);
});

test('donation casts attributes correctly', function () {
    $donation = new Donation();
    
    expect($donation->getCasts())->toHaveKey('amount');
});

