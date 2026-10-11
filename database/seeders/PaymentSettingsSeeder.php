<?php

namespace Database\Seeders;

use App\Models\PaymentSetting;
use Illuminate\Database\Seeder;

class PaymentSettingsSeeder extends Seeder
{
    public function run(): void
    {
        PaymentSetting::query()->firstOrCreate(
            ['id' => 1],
            [
                'platform_fee_percent' => config('payments.defaults.platform_fee_percent', 5),
                'platform_fee_fixed' => config('payments.defaults.platform_fee_fixed', 0),
                'mp_pix_max_amount' => config('payments.defaults.mp_pix_max_amount', 200),
                'mp_pix_fee_percent' => config('payments.defaults.mp_pix_fee_percent', 0.99),
                'asaas_pix_fee_fixed' => config('payments.defaults.asaas_pix_fee_fixed', 1.99),
                'asaas_card_fee_fixed' => config('payments.defaults.asaas_card_fee_fixed', 0.49),
                'asaas_card_fee_percent' => config('payments.defaults.asaas_card_fee_percent', 2.99),
                'public_fee_intro' => 'As taxas abaixo são estimativas com base nos meios de pagamento. O valor líquido creditado na campanha pode variar conforme confirmação do gateway.',
                'public_fee_footnote' => 'Campanhas aprovadas na modalidade "somente taxas do gateway" não incluem comissão da plataforma sobre a doação.',
            ]
        );
    }
}
