<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    protected $fillable = [
        'platform_fee_percent',
        'platform_fee_fixed',
        'mp_pix_max_amount',
        'mp_pix_fee_percent',
        'asaas_pix_fee_fixed',
        'asaas_card_fee_fixed',
        'asaas_card_fee_percent',
        'platform_asaas_wallet_id',
        'public_fee_intro',
        'public_fee_footnote',
    ];

    protected $casts = [
        'platform_fee_percent' => 'float',
        'platform_fee_fixed' => 'float',
        'mp_pix_max_amount' => 'float',
        'mp_pix_fee_percent' => 'float',
        'asaas_pix_fee_fixed' => 'float',
        'asaas_card_fee_fixed' => 'float',
        'asaas_card_fee_percent' => 'float',
    ];
}
