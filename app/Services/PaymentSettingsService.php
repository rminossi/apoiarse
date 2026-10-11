<?php

namespace App\Services;

use App\Models\PaymentSetting;
use Illuminate\Support\Facades\Cache;

class PaymentSettingsService
{
    private const CACHE_KEY = 'payment_settings';

    private const CACHE_TTL = 600;

    public function get(): PaymentSetting
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $settings = PaymentSetting::query()->find(1);

            if (! $settings) {
                $settings = PaymentSetting::query()->create([
                    'platform_fee_percent' => config('payments.defaults.platform_fee_percent', 5),
                    'platform_fee_fixed' => config('payments.defaults.platform_fee_fixed', 0),
                    'mp_pix_max_amount' => config('payments.defaults.mp_pix_max_amount', 200),
                    'mp_pix_fee_percent' => config('payments.defaults.mp_pix_fee_percent', 0.99),
                    'asaas_pix_fee_fixed' => config('payments.defaults.asaas_pix_fee_fixed', 1.99),
                    'asaas_card_fee_fixed' => config('payments.defaults.asaas_card_fee_fixed', 0.49),
                    'asaas_card_fee_percent' => config('payments.defaults.asaas_card_fee_percent', 2.99),
                ]);
            }

            return $settings;
        });
    }

    public function forgetCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    public function update(array $data): PaymentSetting
    {
        $settings = $this->get();
        $settings->fill($data);
        $settings->save();
        $this->forgetCache();

        return $settings->fresh();
    }
}
