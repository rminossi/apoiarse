<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Donation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'campaign_id',
        'amount',
        'asaas_operation_id',
        'mp_operation_id',
        'payment_method',
        'status',
        'pix_qrcode',
        'pix_key',
        'gateway',
        'platform_fee',
        'gateway_fee',
        'net_amount',
        'fee_mode_applied',
        'split_applied',
        'fee_breakdown',
    ];

    protected $casts = [
        'platform_fee' => 'float',
        'gateway_fee' => 'float',
        'net_amount' => 'float',
        'split_applied' => 'boolean',
        'fee_breakdown' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
