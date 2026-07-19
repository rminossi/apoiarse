@extends('layouts.public')

@section('content')
<div class="page-container py-6 sm:py-10">
    <div class="grid gap-6 lg:grid-cols-3 lg:gap-8">
        {{-- Sidebar first on mobile (donate CTA) --}}
        <div class="order-1 space-y-4 lg:order-2 lg:sticky lg:top-24 lg:col-span-1 lg:self-start lg:space-y-6">
            <x-donation-wizard :campaign="$campaign" />

            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:p-6">
                <h3 class="font-semibold text-stone-900">Compartilhar</h3>
                <x-share-buttons :url="route('web.campaign', $campaign->slug)" :title="$campaign->title" class="mt-4" />
            </div>

            <div class="rounded-xl bg-brand-50 p-4 ring-1 ring-brand-100 sm:p-6">
                <h3 class="font-semibold text-brand-800">Pagamento seguro</h3>
                <ul class="mt-3 space-y-2 text-sm text-brand-700">
                    <li>✓ PIX instantâneo</li>
                    <li>✓ Cartão de crédito</li>
                    <li>✓ Plataforma verificada</li>
                </ul>
            </div>
        </div>

        {{-- Main content --}}
        <div class="order-2 space-y-5 lg:order-1 lg:col-span-2 lg:space-y-8">
            <div class="overflow-hidden rounded-xl bg-white shadow-sm ring-1 ring-stone-200">
                <img src="{{ url($campaign->cover()) }}" alt="{{ $campaign->title }}" class="aspect-[4/3] w-full object-cover sm:aspect-video">
            </div>

            <div>
                @if($campaign->category)
                    <x-category-badge :category="$campaign->category" class="mb-3" />
                @endif
                <h1 class="text-2xl font-bold leading-tight text-stone-900 sm:text-3xl">{{ $campaign->title }}</h1>
                @if($campaign->user)
                    <p class="mt-2 text-sm text-stone-500">
                        Por
                        @if($campaign->user->public_slug)
                            <a href="{{ route('web.creator', $campaign->user->public_slug) }}" class="font-medium text-brand-600 hover:underline">{{ $campaign->user->name }}</a>
                        @else
                            {{ $campaign->user->name }}
                        @endif
                    </p>
                @endif
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:p-6">
                <x-progress-bar :campaign="$campaign" size="lg" />
                <div class="mt-4 flex flex-wrap gap-x-4 gap-y-2 text-sm text-stone-600">
                    <span><strong>{{ $campaign->supporters_count }}</strong> apoiadores</span>
                    @if($campaign->days_remaining !== null)
                        <span><strong>{{ $campaign->days_remaining }}</strong> dias restantes</span>
                    @endif
                </div>
            </div>

            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:p-6">
                <h2 class="text-lg font-semibold text-stone-900 sm:text-xl">História</h2>
                <div class="prose prose-stone mt-3 max-w-none text-sm leading-relaxed sm:text-base">
                    {!! nl2br(e(strip_tags($campaign->description))) !!}
                </div>
            </div>

            @if($campaign->updates->isNotEmpty())
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:p-6">
                <h2 class="text-lg font-semibold text-stone-900 sm:text-xl">Atualizações</h2>
                <div class="mt-5 space-y-5 border-l-2 border-brand-200 pl-4 sm:mt-6 sm:space-y-6 sm:pl-6">
                    @foreach($campaign->updates as $update)
                        <div>
                            <time class="text-xs text-stone-500">{{ $update->created_at->format('d/m/Y') }}</time>
                            <h3 class="font-semibold text-stone-900">{{ $update->title }}</h3>
                            <p class="mt-1 text-sm leading-relaxed text-stone-600">{!! nl2br(e(strip_tags($update->body))) !!}</p>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif

            @if($supporters->isNotEmpty())
            <div class="rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:p-6">
                <h2 class="text-lg font-semibold text-stone-900 sm:text-xl">Apoiadores</h2>
                <div class="mt-4 divide-y divide-stone-100">
                    @foreach($supporters->take(20) as $donation)
                        <div class="flex items-center justify-between gap-3 py-3 text-sm">
                            <span class="min-w-0 truncate font-medium text-stone-800">{{ explode(' ', trim($donation->user->name ?? 'Anônimo'))[0] }}</span>
                            <span class="shrink-0 font-semibold text-brand-700">R$ {{ number_format($donation->amount, 2, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/frontend/css/card-js.min.css') }}">
@endpush

@push('scripts')
<script src="{{ asset('assets/frontend/js/card-js.min.js') }}"></script>
@endpush
