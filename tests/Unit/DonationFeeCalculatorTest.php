<?php

namespace Tests\Unit;

use App\Exceptions\InvalidDonationAmountException;
use App\Models\Campaign;
use App\Models\PaymentSetting;
use App\Models\User;
use App\Services\DonationFeeCalculator;
use App\Services\PaymentSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DonationFeeCalculatorTest extends TestCase
{
    use RefreshDatabase;

    private DonationFeeCalculator $calculator;

    private PaymentSetting $settings;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Database\Seeders\PaymentSettingsSeeder::class);
        $this->settings = PaymentSetting::query()->findOrFail(1);
        $this->calculator = new DonationFeeCalculator(new PaymentSettingsService);
    }

    public function test_standard_mode_pix_mercadopago(): void
    {
        $campaign = Campaign::factory()->create(['fee_mode' => 'standard']);

        $result = $this->calculator->calculate(100, 'pix', $campaign, $this->settings);

        $this->assertSame('mercadopago', $result['gateway']);
        $this->assertEqualsWithDelta(0.99, $result['gateway_fee'], 0.01);
        $this->assertEqualsWithDelta(5.0, $result['platform_fee'], 0.01);
        $this->assertEqualsWithDelta(94.01, $result['net_amount'], 0.01);
        $this->assertSame('standard', $result['fee_mode_applied']);
    }

    public function test_gateway_only_mode_zero_platform_fee(): void
    {
        $campaign = Campaign::factory()->create(['fee_mode' => 'gateway_only']);

        $result = $this->calculator->calculate(100, 'pix', $campaign, $this->settings);

        $this->assertSame(0.0, $result['platform_fee']);
        $this->assertEqualsWithDelta(99.01, $result['net_amount'], 0.01);
    }

    public function test_pix_uses_asaas_above_threshold(): void
    {
        $campaign = Campaign::factory()->create();
        $result = $this->calculator->calculate(201, 'pix', $campaign, $this->settings);

        $this->assertSame('asaas', $result['gateway']);
        $this->assertSame(1.99, $result['gateway_fee']);
    }

    public function test_card_uses_asaas_fees(): void
    {
        $campaign = Campaign::factory()->create();
        $result = $this->calculator->calculate(100, 'card', $campaign, $this->settings);

        $this->assertSame('asaas', $result['gateway']);
        $this->assertEqualsWithDelta(3.48, $result['gateway_fee'], 0.01);
    }

    public function test_rejects_amount_that_does_not_cover_fees(): void
    {
        $this->settings->update(['mp_pix_max_amount' => 0, 'asaas_pix_fee_fixed' => 50, 'platform_fee_percent' => 50]);
        $campaign = Campaign::factory()->create(['fee_mode' => 'standard']);

        $this->expectException(InvalidDonationAmountException::class);
        $this->calculator->calculate(10, 'pix', $campaign, $this->settings->fresh());
    }

    public function test_build_asaas_split_when_wallets_present(): void
    {
        $owner = User::factory()->create(['asaas_wallet_id' => 'wal_creator']);
        $campaign = Campaign::factory()->create([
            'user_id' => $owner->id,
            'split_payout_enabled' => true,
        ]);
        $this->settings->update(['platform_asaas_wallet_id' => 'wal_platform']);

        $fee = $this->calculator->calculate(100, 'pix', $campaign, $this->settings->fresh());
        $split = $this->calculator->buildAsaasSplit($campaign, $fee, $this->settings->fresh());

        $this->assertCount(2, $split);
        $this->assertSame('wal_creator', $split[0]['walletId']);
    }
}
