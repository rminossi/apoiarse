@props(['title', 'description' => null, 'action' => null, 'actionLabel' => 'Criar campanha'])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center rounded-xl border border-dashed border-stone-300 bg-stone-50 px-6 py-16 text-center']) }}>
    <svg class="mb-4 h-12 w-12 text-stone-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
    </svg>
    <h3 class="text-lg font-semibold text-stone-900">{{ $title }}</h3>
    @if($description)
        <p class="mt-2 max-w-md text-sm text-stone-500">{{ $description }}</p>
    @endif
    @if($action)
        <a href="{{ $action }}" class="btn-primary mt-6">{{ $actionLabel }}</a>
    @endif
</div>
