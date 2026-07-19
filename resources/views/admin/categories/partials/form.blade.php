<label class="label">
    <span class="legend">*Nome:</span>
    <input type="text" name="name" value="{{ old('name', $category->name ?? '') }}" required>
</label>
<label class="label">
    <span class="legend">Ícone (opcional):</span>
    <input type="text" name="icon" value="{{ old('icon', $category->icon ?? '') }}">
</label>
<label class="label">
    <span class="legend">*Cor (hex):</span>
    <input type="color" name="color" value="{{ old('color', $category->color ?? '#059669') }}">
</label>
<label class="label">
    <span class="legend">Descrição:</span>
    <textarea name="description" rows="3">{{ old('description', $category->description ?? '') }}</textarea>
</label>
<label class="label">
    <span class="legend">Ordem:</span>
    <input type="number" name="sort_order" min="0" value="{{ old('sort_order', $category->sort_order ?? 0) }}">
</label>
<label class="label">
    <span class="legend">Ativa:</span>
    <select name="is_active">
        <option value="1" @selected(old('is_active', $category->is_active ?? true))>Sim</option>
        <option value="0" @selected(!old('is_active', $category->is_active ?? true))>Não</option>
    </select>
</label>
