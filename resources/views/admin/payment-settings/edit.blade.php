@extends('admin.master.master')
@section('content')
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-money">Taxas e split de pagamentos</h2>
    </header>

    <div class="dash_content_app_box">
        @if(session('message'))
            <div class="message message-green"><p>{{ session('message') }}</p></div>
        @endif
        @if($errors->any())
            <div class="message message-red">
                @foreach($errors->all() as $error)<p>{{ $error }}</p>@endforeach
            </div>
        @endif

        <form class="app_form" method="POST" action="{{ route('admin.payment-settings.update') }}">
            @csrf
            @method('PUT')

            <h3 class="mb-2">Comissão da plataforma</h3>
            <div class="label_g2">
                <label class="label">
                    <span class="legend">Percentual (%)</span>
                    <input type="number" step="0.01" name="platform_fee_percent" value="{{ old('platform_fee_percent', $settings->platform_fee_percent) }}" required>
                </label>
                <label class="label">
                    <span class="legend">Valor fixo (R$)</span>
                    <input type="number" step="0.01" name="platform_fee_fixed" value="{{ old('platform_fee_fixed', $settings->platform_fee_fixed) }}" required>
                </label>
            </div>

            <h3 class="mt-4 mb-2">Mercado Pago (PIX)</h3>
            <div class="label_g2">
                <label class="label">
                    <span class="legend">Valor máximo PIX via MP (R$)</span>
                    <input type="number" step="0.01" name="mp_pix_max_amount" value="{{ old('mp_pix_max_amount', $settings->mp_pix_max_amount) }}" required>
                </label>
                <label class="label">
                    <span class="legend">Taxa PIX MP (%)</span>
                    <input type="number" step="0.01" name="mp_pix_fee_percent" value="{{ old('mp_pix_fee_percent', $settings->mp_pix_fee_percent) }}" required>
                </label>
            </div>

            <h3 class="mt-4 mb-2">Asaas</h3>
            <div class="label_g3">
                <label class="label">
                    <span class="legend">PIX fixo (R$)</span>
                    <input type="number" step="0.01" name="asaas_pix_fee_fixed" value="{{ old('asaas_pix_fee_fixed', $settings->asaas_pix_fee_fixed) }}" required>
                </label>
                <label class="label">
                    <span class="legend">Cartão fixo (R$)</span>
                    <input type="number" step="0.01" name="asaas_card_fee_fixed" value="{{ old('asaas_card_fee_fixed', $settings->asaas_card_fee_fixed) }}" required>
                </label>
                <label class="label">
                    <span class="legend">Cartão (%)</span>
                    <input type="number" step="0.01" name="asaas_card_fee_percent" value="{{ old('asaas_card_fee_percent', $settings->asaas_card_fee_percent) }}" required>
                </label>
            </div>

            <h3 class="mt-4 mb-2">Split Asaas</h3>
            <label class="label">
                <span class="legend">Wallet ID da plataforma</span>
                <input type="text" name="platform_asaas_wallet_id" value="{{ old('platform_asaas_wallet_id', $settings->platform_asaas_wallet_id) }}">
            </label>

            <h3 class="mt-4 mb-2">Textos públicos</h3>
            <label class="label">
                <span class="legend">Introdução (página da campanha)</span>
                <textarea name="public_fee_intro" rows="3">{{ old('public_fee_intro', $settings->public_fee_intro) }}</textarea>
            </label>
            <label class="label">
                <span class="legend">Nota de rodapé</span>
                <textarea name="public_fee_footnote" rows="2">{{ old('public_fee_footnote', $settings->public_fee_footnote) }}</textarea>
            </label>

            <div class="text-right mt-3">
                <button type="submit" class="btn btn-green icon-check-square-o">Salvar</button>
            </div>
        </form>

        <div class="mt-4 message message-blue">
            <p><strong>Prévia (modalidade padrão)</strong></p>
            <p>PIX R$ 50: líquido R$ {{ number_format($previews['pix_50_standard']['net_amount'], 2, ',', '.') }}</p>
            <p>PIX R$ 201: líquido R$ {{ number_format($previews['pix_201_standard']['net_amount'], 2, ',', '.') }}</p>
            <p>Cartão R$ 100: líquido R$ {{ number_format($previews['card_100_standard']['net_amount'], 2, ',', '.') }}</p>
            <p><strong>Somente gateway (PIX R$ 100):</strong> líquido R$ {{ number_format($previews['pix_100_gateway_only']['net_amount'], 2, ',', '.') }}</p>
        </div>
    </div>
</section>
@endsection
