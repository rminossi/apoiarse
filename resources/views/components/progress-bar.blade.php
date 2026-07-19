@props(['campaign', 'size' => 'md'])

@php
    $barHeight = $size === 'lg' ? 'h-3' : 'h-2';
@endphp

<div {{ $attributes->merge(['class' => 'space-y-2']) }}>
    <div class="flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between sm:gap-2">
        <div class="min-w-0 text-sm">
            <span class="font-bold text-brand-700">R$ {{ $campaign->total_donations }}</span>
            <span class="text-stone-500"> de R$ {{ $campaign->goal ?? '0,00' }}</span>
        </div>
        <span class="text-sm font-semibold text-stone-700 sm:shrink-0">{{ $campaign->progress_percent }}%</span>
    </div>
    <div class="w-full overflow-hidden rounded-full bg-stone-200 {{ $barHeight }}">
        <div class="{{ $barHeight }} rounded-full bg-brand-600 transition-all duration-500"
             style="width: {{ min(100, $campaign->progress_percent) }}%"></div>
    </div>
</div>
