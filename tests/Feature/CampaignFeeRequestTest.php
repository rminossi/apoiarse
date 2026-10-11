<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignFeeRequestTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\CategorySeeder::class);
        $this->seed(\Database\Seeders\PaymentSettingsSeeder::class);
    }

    public function test_creator_gateway_only_request_sets_pending_and_inactive(): void
    {
        $user = User::factory()->create();
        $category = \App\Models\Category::first();

        $response = $this->actingAs($user)->post(route('usuario.campanhas.store'), [
            'title' => 'Campanha taxas',
            'description' => 'Descrição longa o suficiente',
            'type' => 'other',
            'category_id' => $category->id,
            'status' => 1,
            'request_gateway_only' => 1,
        ]);

        $response->assertRedirect();

        $campaign = Campaign::query()->where('title', 'Campanha taxas')->first();
        $this->assertNotNull($campaign);
        $this->assertSame('pending', $campaign->fee_mode_request);
        $this->assertSame(2, (int) $campaign->status);
    }

    public function test_admin_can_approve_gateway_only(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $campaign = Campaign::factory()->create([
            'fee_mode_request' => 'pending',
            'status' => 2,
        ]);

        $response = $this->actingAs($admin)->post(route('admin.campaigns.review-fee-mode', $campaign), [
            'decision' => 'approve_gateway_only',
            'activate' => 1,
        ]);

        $response->assertRedirect();
        $campaign->refresh();
        $this->assertSame('gateway_only', $campaign->fee_mode);
        $this->assertSame('approved', $campaign->fee_mode_request);
        $this->assertSame(1, (int) $campaign->status);
    }
}
