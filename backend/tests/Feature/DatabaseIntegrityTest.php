<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DatabaseIntegrityTest extends TestCase
{
    use RefreshDatabase;

    public function test_cascade_delete_donations_when_campaign_deleted(): void
    {
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create(['campaign_id' => $campaign->id]);

        $campaign->delete();

        $this->assertDatabaseMissing('donations', ['id' => $donation->id]);
    }

    public function test_cascade_delete_donations_when_user_deleted(): void
    {
        $user = User::factory()->create();
        $donation = Donation::factory()->create(['user_id' => $user->id]);

        $user->delete();

        $this->assertDatabaseMissing('donations', ['id' => $donation->id]);
    }

    public function test_campaign_user_relationship_integrity(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);

        $this->assertEquals($user->id, $campaign->user_id);
        $this->assertTrue($user->campaigns->contains($campaign));
    }

    public function test_donation_relationships_integrity(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->create();
        $donation = Donation::factory()->create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id
        ]);

        $this->assertEquals($user->id, $donation->user_id);
        $this->assertEquals($campaign->id, $donation->campaign_id);
        $this->assertTrue($user->donations->contains($donation));
        $this->assertTrue($campaign->donations->contains($donation));
    }

    public function test_unique_email_constraint(): void
    {
        User::factory()->create(['email' => 'test@example.com']);

        $this->expectException(\Illuminate\Database\QueryException::class);
        User::factory()->create(['email' => 'test@example.com']);
    }
}