@props(['campaign', 'paymentSettings'])

@php
    $feeCalculator = app(\App\Services\DonationFeeCalculator::class);
    $examplePix = $feeCalculator->calculate(100, 'pix', $campaign, $paymentSettings);
    $exampleCard = $feeCalculator->calculate(100, 'card', $campaign, $paymentSettings);
    $modeLabel = $campaign->effectiveFeeMode() === 'gateway_only'
        ? 'Somente taxas do gateway (sem comissão da plataforma)'
        : 'Padrão (comissão da plataforma + taxas do gateway)';
@endphp

<div {{ $attributes->merge(['class' => 'rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:p-6']) }}>
    <h3 class="font-semibold text-stone-900">Taxas desta campanha</h3>
    <p class="mt-2 text-sm text-stone-600">{{ $paymentSettings->public_fee_intro }}</p>

    <p class="mt-3 text-sm font-medium text-brand-700">{{ $modeLabel }}</p>

    <dl class="mt-4 space-y-2 text-sm text-stone-700">
        <div class="flex justify-between gap-2">
            <dt>Comissão plataforma</dt>
            <dd>{{ number_format($paymentSettings->platform_fee_percent, 2, ',', '.') }}%
                @if($campaign->effectiveFeeMode() === 'gateway_only')
                    <span class="text-brand-600">(isenta nesta campanha)</span>
                @endif
            </dd>
        </div>
        <div class="flex justify-between gap-2">
            <dt>PIX Mercado Pago (até R$ {{ number_format($paymentSettings->mp_pix_max_amount, 0, ',', '.') }})</dt>
            <dd>{{ number_format($paymentSettings->mp_pix_fee_percent, 2, ',', '.') }}%</dd>
        </div>
        <div class="flex justify-between gap-2">
            <dt>PIX Asaas</dt>
            <dd>R$ {{ number_format($paymentSettings->asaas_pix_fee_fixed, 2, ',', '.') }}</dd>
        </div>
        <div class="flex justify-between gap-2">
            <dt>Cartão Asaas</dt>
            <dd>R$ {{ number_format($paymentSettings->asaas_card_fee_fixed, 2, ',', '.') }} + {{ number_format($paymentSettings->asaas_card_fee_percent, 2, ',', '.') }}%</dd>
        </div>
    </dl>

    <div class="mt-4 rounded-lg bg-stone-50 p-3 text-xs text-stone-600">
        <p class="font-semibold text-stone-800">Exemplo doação de R$ 100,00</p>
        <p class="mt-1">PIX: campanha recebe ~ R$ {{ number_format($examplePix['net_amount'], 2, ',', '.') }}
            (gateway ~ R$ {{ number_format($examplePix['gateway_fee'], 2, ',', '.') }},
            plataforma ~ R$ {{ number_format($examplePix['platform_fee'], 2, ',', '.') }})</p>
        <p>Cartão: campanha recebe ~ R$ {{ number_format($exampleCard['net_amount'], 2, ',', '.') }}</p>
    </div>

    @if($paymentSettings->public_fee_footnote)
        <p class="mt-3 text-xs text-stone-500">{{ $paymentSettings->public_fee_footnote }}</p>
    @endif

    @if($campaign->split_payout_enabled)
        <p class="mt-2 text-xs text-brand-700">Repasse automático via Asaas habilitado para esta campanha (quando aplicável).</p>
    @endif
</div>
