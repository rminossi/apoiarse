<div class="label_g2 mt-2">
    <label class="label">
        <span class="legend">Modalidade de taxas (admin)</span>
        <select name="fee_mode">
            <option value="standard" @selected(old('fee_mode', $campaign->fee_mode ?? 'standard') === 'standard')>Padrão</option>
            <option value="gateway_only" @selected(old('fee_mode', $campaign->fee_mode ?? '') === 'gateway_only')>Somente gateway</option>
        </select>
    </label>
    <label class="label">
        <span class="legend">Repasse automático (split)</span>
        <select name="split_payout_enabled">
            <option value="0" @selected(!old('split_payout_enabled', $campaign->split_payout_enabled ?? false))>Não</option>
            <option value="1" @selected(old('split_payout_enabled', $campaign->split_payout_enabled ?? false))>Sim</option>
        </select>
    </label>
</div>
