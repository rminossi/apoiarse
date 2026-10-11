<?php

namespace App\Services;

use App\Exceptions\InvalidDonationAmountException;
use App\Models\Campaign;
use App\Models\PaymentSetting;

class DonationFeeCalculator
{
    public function __construct(
        private PaymentSettingsService $settingsService
    ) {}

    /**
     * @return array{
     *   amount: float,
     *   gateway: string,
     *   payment_method: string,
     *   gateway_fee: float,
     *   platform_fee: float,
     *   net_amount: float,
     *   fee_mode_applied: string,
     *   breakdown: array<string, mixed>
     * }
     */
    public function calculate(float $amount, string $paymentMethod, Campaign $campaign, ?PaymentSetting $settings = null): array
    {
        $settings ??= $this->settingsService->get();
        $paymentMethod = strtolower($paymentMethod) === 'cc' ? 'card' : strtolower($paymentMethod);

        if ($amount <= 0) {
            throw new InvalidDonationAmountException('Valor inválido.');
        }

        $gateway = $this->resolveGateway($amount, $paymentMethod, $settings);
        $gatewayFee = $this->gatewayFee($amount, $gateway, $paymentMethod, $settings);
        $feeMode = $campaign->effectiveFeeMode();
        $platformFee = $feeMode === 'gateway_only'
            ? 0.0
            : $this->platformFee($amount, $settings);

        $net = round($amount - $platformFee - $gatewayFee, 2);

        if ($net < 0) {
            throw new InvalidDonationAmountException(
                'Valor da doação é insuficiente para cobrir as taxas. Informe um valor maior.'
            );
        }

        return [
            'amount' => round($amount, 2),
            'gateway' => $gateway,
            'payment_method' => $paymentMethod,
            'gateway_fee' => round($gatewayFee, 2),
            'platform_fee' => round($platformFee, 2),
            'net_amount' => $net,
            'fee_mode_applied' => $feeMode,
            'breakdown' => [
                'settings_snapshot' => [
                    'platform_fee_percent' => $settings->platform_fee_percent,
                    'platform_fee_fixed' => $settings->platform_fee_fixed,
                    'mp_pix_max_amount' => $settings->mp_pix_max_amount,
                    'mp_pix_fee_percent' => $settings->mp_pix_fee_percent,
                    'asaas_pix_fee_fixed' => $settings->asaas_pix_fee_fixed,
                    'asaas_card_fee_fixed' => $settings->asaas_card_fee_fixed,
                    'asaas_card_fee_percent' => $settings->asaas_card_fee_percent,
                ],
                'campaign_fee_mode' => $feeMode,
            ],
        ];
    }

    public function resolveGateway(float $amount, string $paymentMethod, PaymentSetting $settings): string
    {
        if ($paymentMethod === 'card') {
            return 'asaas';
        }

        return $amount <= (float) $settings->mp_pix_max_amount ? 'mercadopago' : 'asaas';
    }

    private function gatewayFee(float $amount, string $gateway, string $paymentMethod, PaymentSetting $settings): float
    {
        if ($gateway === 'mercadopago') {
            return $amount * ((float) $settings->mp_pix_fee_percent / 100);
        }

        if ($paymentMethod === 'card') {
            return (float) $settings->asaas_card_fee_fixed
                + ($amount * ((float) $settings->asaas_card_fee_percent / 100));
        }

        return (float) $settings->asaas_pix_fee_fixed;
    }

    private function platformFee(float $amount, PaymentSetting $settings): float
    {
        return (float) $settings->platform_fee_fixed
            + ($amount * ((float) $settings->platform_fee_percent / 100));
    }

    /**
     * @return list<array{walletId: string, fixedValue: float}>
     */
    public function buildAsaasSplit(Campaign $campaign, array $feeResult, PaymentSetting $settings): array
    {
        $creatorWallet = $campaign->user?->asaas_wallet_id;
        $platformWallet = $settings->platform_asaas_wallet_id;

        if (! $creatorWallet || ! $platformWallet) {
            return [];
        }

        $split = [
            ['walletId' => $creatorWallet, 'fixedValue' => $feeResult['net_amount']],
        ];

        $platformShare = round($feeResult['platform_fee'] + $feeResult['gateway_fee'], 2);
        if ($platformShare > 0) {
            $split[] = ['walletId' => $platformWallet, 'fixedValue' => $platformShare];
        }

        return $split;
    }

    public function canApplyAsaasSplit(Campaign $campaign, array $feeResult, PaymentSetting $settings): bool
    {
        if (! $campaign->splitPayoutActive()) {
            return false;
        }

        if ($feeResult['gateway'] !== 'asaas') {
            return false;
        }

        $split = $this->buildAsaasSplit($campaign, $feeResult, $settings);

        return count($split) > 0;
    }
}
