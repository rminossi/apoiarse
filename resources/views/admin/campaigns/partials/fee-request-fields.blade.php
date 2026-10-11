@php
    $settings = $paymentSettings ?? null;
@endphp
<div class="message message-blue mt-2 mb-2">
    <h3>Como funcionam as taxas</h3>
    @if($settings)
        <p class="text-sm">Modalidade padrão: comissão de {{ number_format($settings->platform_fee_percent, 2, ',', '.') }}% da plataforma + taxas do meio de pagamento.</p>
        <p class="text-sm">PIX até R$ {{ number_format($settings->mp_pix_max_amount, 2, ',', '.') }} via Mercado Pago; acima disso ou cartão via Asaas.</p>
        <p class="text-sm"><a href="{{ route('web.sobre') }}" target="_blank">Saiba mais na página Sobre</a></p>
    @endif

    @if(!auth()->user()?->is_admin)
        <label class="label mt-2">
            <input type="checkbox" name="request_gateway_only" value="1" @checked(old('request_gateway_only'))>
            Solicitar modalidade <strong>somente taxas do gateway</strong> (sujeita à análise da equipe)
        </label>
        <label class="label">
            <input type="checkbox" name="request_split_payout" value="1" @checked(old('request_split_payout'))>
            Solicitar <strong>repasse automático</strong> ao receber via Asaas (sujeita à análise e cadastro de carteira)
        </label>
        @if(isset($campaign) && $campaign->hasPendingFeeReview())
            <p class="text-orange mt-1"><strong>Aguardando análise</strong> de uma ou mais solicitações.</p>
        @endif
        @if(isset($campaign) && $campaign->fee_mode_request === 'rejected')
            <p class="text-red">Solicitação gateway-only recusada.@if($campaign->fee_review_notes) {{ $campaign->fee_review_notes }}@endif</p>
        @endif
    @endif
</div>
