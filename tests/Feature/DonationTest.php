<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Tests\TestCase;

class DonationTest extends TestCase
{
    public function test_unauthenticated_user_cannot_request_pix(): void
    {
        $campaign = Campaign::factory()->create();

        $response = $this->postJson(route('web.getPixQrCode'), [
            'campaign_id' => $campaign->id,
            'amount' => 'R$ 50,00',
        ]);

        $response->assertUnauthorized();
    }

    public function test_cannot_donate_to_inactive_campaign(): void
    {
        $user = User::factory()->create();
        $campaign = Campaign::factory()->inactive()->create();

        $response = $this->actingAs($user)
            ->postJson(route('web.getPixQrCode'), [
                'campaign_id' => $campaign->id,
                'amount' => 'R$ 50,00',
            ]);

        $response->assertUnprocessable()
            ->assertJson(['error' => 'Esta campanha não está ativa para receber doações.']);
    }

    public function test_my_donations_requires_authentication(): void
    {
        $response = $this->get(route('web.my-donations'));

        $response->assertRedirect(route('sessao.login'));
    }
}
