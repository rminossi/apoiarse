@php
    $creator = $campaign->user;
@endphp
<div class="message message-blue mt-2 mb-2">
    <h3 class="mb-1">Modalidade de taxas</h3>
    <p>Atual: <strong>{{ $campaign->fee_mode === 'gateway_only' ? 'Somente taxas do gateway' : 'Padrão (plataforma + gateway)' }}</strong></p>
    @if($campaign->fee_mode_request === 'pending')
        <p class="text-orange"><strong>Solicitação pendente:</strong> somente taxas do gateway.</p>
    @elseif($campaign->fee_mode_request === 'rejected')
        <p>Solicitação gateway-only recusada.</p>
    @endif
    @if($campaign->fee_review_notes)
        <p class="text-sm">Observação admin: {{ $campaign->fee_review_notes }}</p>
    @endif

    @if($campaign->fee_mode_request === 'pending')
        <form method="POST" action="{{ route('admin.campaigns.review-fee-mode', $campaign) }}" class="mt-2">
            @csrf
            <label class="label"><textarea name="fee_review_notes" rows="2" placeholder="Observação (opcional)"></textarea></label>
            <label class="label"><input type="checkbox" name="activate" value="1"> Ativar campanha ao aprovar</label>
            <div class="d-flex gap-1">
                <button type="submit" name="decision" value="approve_gateway_only" class="btn btn-green btn-small">Aprovar gateway-only</button>
                <button type="submit" name="decision" value="keep_standard" class="btn btn-blue btn-small">Manter padrão</button>
                <button type="submit" name="decision" value="reject" class="btn btn-red btn-small">Recusar solicitação</button>
            </div>
        </form>
    @endif

</div>

<div class="message message-blue mt-2 mb-2">
    <h3 class="mb-1">Repasse automático (split Asaas)</h3>
    <p>Status: <strong>{{ $campaign->split_payout_enabled ? 'Ativo' : 'Inativo (ledger)' }}</strong></p>
    @if($campaign->split_payout_request === 'pending')
        <p class="text-orange"><strong>Solicitação pendente</strong> de repasse automático.</p>
    @endif
    @if(empty($creator?->asaas_wallet_id))
        <p class="text-red">Criador sem wallet Asaas cadastrada — split automático indisponível.</p>
    @else
        <p>Wallet criador: {{ $creator->asaas_wallet_id }}</p>
    @endif

    @if($campaign->split_payout_request === 'pending')
        <form method="POST" action="{{ route('admin.campaigns.review-split-payout', $campaign) }}" class="mt-2">
            @csrf
            <label class="label"><textarea name="fee_review_notes" rows="2" placeholder="Observação (opcional)"></textarea></label>
            <button type="submit" name="decision" value="approve" class="btn btn-green btn-small">Aprovar split</button>
            <button type="submit" name="decision" value="reject" class="btn btn-red btn-small">Recusar split</button>
        </form>
    @endif
</div>
