<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Services\DonationFeeCalculator;
use App\Services\PaymentSettingsService;
use Illuminate\Http\Request;

class PaymentSettingsController extends Controller
{
    public function __construct(
        private PaymentSettingsService $settingsService,
        private DonationFeeCalculator $feeCalculator
    ) {}

    public function edit()
    {
        $settings = $this->settingsService->get();
        $previewCampaign = new Campaign(['fee_mode' => 'standard']);
        $previewGatewayOnly = new Campaign(['fee_mode' => 'gateway_only']);

        $previews = [
            'pix_50_standard' => $this->feeCalculator->calculate(50, 'pix', $previewCampaign, $settings),
            'pix_201_standard' => $this->feeCalculator->calculate(201, 'pix', $previewCampaign, $settings),
            'card_100_standard' => $this->feeCalculator->calculate(100, 'card', $previewCampaign, $settings),
            'pix_100_gateway_only' => $this->feeCalculator->calculate(100, 'pix', $previewGatewayOnly, $settings),
        ];

        return view('admin.payment-settings.edit', compact('settings', 'previews'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'platform_fee_percent' => 'required|numeric|min:0|max:100',
            'platform_fee_fixed' => 'required|numeric|min:0',
            'mp_pix_max_amount' => 'required|numeric|min:0.01',
            'mp_pix_fee_percent' => 'required|numeric|min:0|max:100',
            'asaas_pix_fee_fixed' => 'required|numeric|min:0',
            'asaas_card_fee_fixed' => 'required|numeric|min:0',
            'asaas_card_fee_percent' => 'required|numeric|min:0|max:100',
            'platform_asaas_wallet_id' => 'nullable|string|max:255',
            'public_fee_intro' => 'nullable|string|max:5000',
            'public_fee_footnote' => 'nullable|string|max:5000',
        ]);

        $this->settingsService->update($validated);

        return redirect()->route('admin.payment-settings.edit')
            ->with('message', 'Taxas e split atualizados com sucesso!');
    }
}
