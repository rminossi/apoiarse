<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Tests\TestCase;

class WebhookTest extends TestCase
{
    public function test_asaas_webhook_rejects_invalid_token(): void
    {
        config(['payments.asaas.webhook_token' => 'valid-token']);

        $response = $this->postJson('/api/webhook', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_123'],
        ], [
            'asaas-access-token' => 'invalid-token',
        ]);

        $response->assertUnauthorized();
    }

    public function test_asaas_webhook_updates_donation_status(): void
    {
        config(['payments.asaas.webhook_token' => 'valid-token']);

        $user = User::factory()->create();
        $campaign = Campaign::factory()->create(['user_id' => $user->id]);
        $donation = Donation::create([
            'user_id' => $user->id,
            'campaign_id' => $campaign->id,
            'amount' => 100,
            'asaas_operation_id' => 'pay_123',
            'payment_method' => 'PIX',
            'status' => 1,
        ]);

        $response = $this->postJson('/api/webhook', [
            'event' => 'PAYMENT_CONFIRMED',
            'payment' => ['id' => 'pay_123'],
        ], [
            'asaas-access-token' => 'valid-token',
        ]);

        $response->assertOk();
        $this->assertEquals(3, $donation->fresh()->status);
    }
}
