@php($categories = $categories ?? \App\Models\Category::active()->get())
<div class="label_g2">
    <label class="label">
        <span class="legend">*Categoria:</span>
        <select name="category_id" required>
            <option value="">Selecione</option>
            @foreach($categories as $category)
                <option value="{{ $category->id }}" @selected(old('category_id', $campaign->category_id ?? '') == $category->id)>{{ $category->name }}</option>
            @endforeach
        </select>
    </label>
    <label class="label">
        <span class="legend">Data de encerramento:</span>
        <input type="date" name="end_date" value="{{ old('end_date', isset($campaign) && $campaign->end_date ? $campaign->end_date->format('Y-m-d') : '') }}">
    </label>
</div>
<label class="label">
    <span class="legend">Descrição curta (cards):</span>
    <input type="text" name="short_description" maxlength="300" placeholder="Resumo para listagens"
           value="{{ old('short_description', $campaign->short_description ?? '') }}">
</label>
@if(auth()->user()?->is_admin)
<div class="label_g2">
    <label class="label">
        <span class="legend">Destaque na home:</span>
        <select name="is_featured">
            <option value="0" @selected(!old('is_featured', $campaign->is_featured ?? false))>Não</option>
            <option value="1" @selected(old('is_featured', $campaign->is_featured ?? false))>Sim</option>
        </select>
    </label>
    <label class="label">
        <span class="legend">Ordem destaque:</span>
        <input type="number" name="featured_order" min="0" value="{{ old('featured_order', $campaign->featured_order ?? 0) }}">
    </label>
</div>
@endif
