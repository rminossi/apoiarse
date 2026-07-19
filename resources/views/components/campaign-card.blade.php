@props(['campaign'])

<a href="{{ route('web.campaign', $campaign->slug) }}" class="card-campaign group flex flex-col h-full">
    <div class="relative aspect-[16/10] overflow-hidden bg-stone-100">
        <img src="{{ url($campaign->cover()) }}" alt="{{ $campaign->title }}"
             class="h-full w-full object-cover transition duration-300 group-hover:scale-105">
        @if($campaign->is_featured)
            <span class="absolute left-3 top-3 rounded-full bg-amber-500 px-2.5 py-1 text-xs font-semibold text-white">Destaque</span>
        @endif
        @if($campaign->category)
            <div class="absolute bottom-3 left-3">
                <x-category-badge :category="$campaign->category" />
            </div>
        @endif
    </div>
    <div class="flex flex-1 flex-col p-4">
        <h3 class="line-clamp-2 text-base font-semibold text-stone-900 group-hover:text-brand-700">{{ $campaign->title }}</h3>
        @if($campaign->short_description)
            <p class="mt-1 line-clamp-2 text-sm text-stone-500">{{ $campaign->short_description }}</p>
        @endif
        <div class="mt-4">
            <x-progress-bar :campaign="$campaign" size="sm" />
        </div>
        <div class="mt-3 flex items-center justify-between text-xs text-stone-500">
            <span>{{ $campaign->supporters_count }} apoiador(es)</span>
            @if($campaign->days_remaining !== null)
                <span>{{ $campaign->days_remaining }} dia(s) restantes</span>
            @endif
        </div>
    </div>
</a>
