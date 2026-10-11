<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Services\MercadoPagoService;
use Tests\TestCase;

class DonationFeeRecordingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PaymentSettingsSeeder::class);
    }

    public function test_pix_donation_records_fee_breakdown_with_mercadopago(): void
    {
        $this->mock(MercadoPagoService::class, function ($mock) {
            $mock->shouldReceive('getQrCodeMercadoPago')
                ->once()
                ->andReturn([
                    'qrCode' => [
                        'encodedImage' => base64_encode('x'),
                        'qrCode' => '000201',
                    ],
                    'operation_id' => 'mp-123',
                ]);
        });

        $user = User::factory()->create(['asaas_id' => 'cus_test']);
        $campaign = Campaign::factory()->create(['fee_mode' => 'standard']);

        $response = $this->actingAs($user)->postJson(route('web.getPixQrCode'), [
            'campaign_id' => $campaign->id,
            'amount' => 'R$ 100,00',
        ]);

        $response->assertOk();
        $donation = Donation::query()->latest('id')->first();
        $this->assertNotNull($donation);
        $this->assertSame('mercadopago', $donation->gateway);
        $this->assertEqualsWithDelta(5.0, (float) $donation->platform_fee, 0.01);
        $this->assertNotNull($donation->net_amount);
        $this->assertIsArray($donation->fee_breakdown);
    }
}
