@extends('layouts.public')

@section('content')
<section class="w-full bg-gradient-to-b from-brand-50 to-stone-50">
    <div class="page-container py-10 sm:py-16 lg:py-24">
        <div class="mx-auto w-full max-w-3xl text-center">
            <h1 class="text-3xl font-extrabold leading-tight tracking-tight text-stone-900 sm:text-4xl lg:text-5xl">
                Apoie causas que <span class="text-brand-600">transformam vidas</span>
            </h1>
            <p class="mt-4 text-base leading-relaxed text-stone-600 sm:text-lg">
                Crie campanhas, compartilhe com sua rede e arrecade com segurança via PIX ou cartão.
            </p>
            <form action="{{ route('web.campaigns') }}" method="GET" class="mt-6 flex w-full flex-col gap-3 sm:mt-8 sm:flex-row sm:gap-2">
                <input type="search" name="q" placeholder="Buscar campanhas..." class="input-field min-w-0 flex-1" value="{{ request('q') }}">
                <button type="submit" class="btn-primary w-full shrink-0 sm:w-auto">Buscar</button>
            </form>
            <div class="mt-5 flex w-full flex-col gap-3 sm:mt-6 sm:flex-row sm:flex-wrap sm:justify-center">
                <a href="{{ route('web.campaigns') }}" class="btn-primary w-full sm:w-auto">Explorar campanhas</a>
                <a href="{{ route('usuario.campanhas.create') }}" class="btn-outline w-full sm:w-auto">Criar campanha</a>
            </div>
        </div>
    </div>
</section>

<section class="w-full border-y border-stone-200 bg-white py-8 sm:py-10">
    <div class="page-container grid grid-cols-3 gap-3 sm:gap-8">
        <div class="text-center">
            <p class="text-lg font-bold text-brand-600 sm:text-3xl">R$ {{ number_format($stats['total_raised'], 2, ',', '.') }}</p>
            <p class="mt-1 text-[11px] text-stone-500 sm:text-sm">Total arrecadado</p>
        </div>
        <div class="text-center">
            <p class="text-lg font-bold text-brand-600 sm:text-3xl">{{ $stats['active_campaigns'] }}</p>
            <p class="mt-1 text-[11px] text-stone-500 sm:text-sm">Campanhas ativas</p>
        </div>
        <div class="text-center">
            <p class="text-lg font-bold text-brand-600 sm:text-3xl">{{ $stats['supporters'] }}</p>
            <p class="mt-1 text-[11px] text-stone-500 sm:text-sm">Apoiadores</p>
        </div>
    </div>
</section>

@if($categories->isNotEmpty())
<section class="w-full py-10 sm:py-16">
    <div class="page-container">
        <h2 class="section-title text-center">Explore por categoria</h2>
        <div class="mt-6 grid grid-cols-2 gap-3 sm:mt-8 sm:grid-cols-3 sm:gap-4 lg:grid-cols-4">
            @foreach($categories as $category)
                <a href="{{ route('web.campaigns', ['category' => $category->slug]) }}"
                   class="flex flex-col items-center rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 transition hover:shadow-md hover:ring-brand-300 sm:p-6">
                    <span class="flex h-10 w-10 items-center justify-center rounded-full text-base font-bold text-white sm:h-12 sm:w-12 sm:text-lg"
                          style="background-color: {{ $category->color }}">{{ mb_substr($category->name, 0, 1) }}</span>
                    <span class="mt-2 text-center text-xs font-semibold leading-snug text-stone-800 sm:mt-3 sm:text-sm">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($featuredCampaigns->isNotEmpty())
<section class="w-full bg-brand-50 py-10 sm:py-16">
    <div class="page-container">
        <h2 class="section-title">Em destaque</h2>
        <p class="section-subtitle">Campanhas selecionadas pela nossa equipe</p>
        <div class="mt-6 grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @foreach($featuredCampaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="w-full py-10 sm:py-16">
    <div class="page-container">
        <h2 class="section-title">Campanhas recentes</h2>
        @if($activeCampaigns->isEmpty())
            <x-empty-state title="Nenhuma campanha ativa" description="Seja o primeiro a criar uma campanha!" :action="route('usuario.campanhas.create')" class="mt-8" />
        @else
            <div class="mt-6 grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
                @foreach($activeCampaigns as $campaign)
                    <x-campaign-card :campaign="$campaign" />
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('web.campaigns') }}" class="btn-secondary w-full sm:w-auto">Ver todas</a>
            </div>
        @endif
    </div>
</section>

@if(!empty($howItWorks))
<section class="w-full border-t border-stone-200 bg-white py-10 sm:py-16">
    <div class="page-container">
        <h2 class="section-title text-center">Como funciona</h2>
        <div class="mt-8 grid gap-6 sm:mt-12 sm:gap-8 md:grid-cols-3">
            @foreach($howItWorks as $step)
                <div class="rounded-xl bg-stone-50 p-5 text-center ring-1 ring-stone-200 sm:bg-transparent sm:p-0 sm:ring-0">
                    <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-brand-600 text-lg font-bold text-white sm:h-14 sm:w-14 sm:text-xl">{{ $step['step'] ?? '' }}</div>
                    <h3 class="mt-4 text-base font-semibold text-stone-900 sm:text-lg">{{ $step['title'] ?? '' }}</h3>
                    <p class="mt-2 text-sm leading-relaxed text-stone-600">{{ $step['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($finishedCampaigns->isNotEmpty())
<section class="w-full py-10 sm:py-16">
    <div class="page-container">
        <h2 class="section-title">Campanhas encerradas</h2>
        <div class="mt-6 grid grid-cols-1 gap-4 sm:mt-8 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @foreach($finishedCampaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($testimonials))
<section class="w-full bg-stone-100 py-10 sm:py-16">
    <div class="page-container">
        <h2 class="section-title text-center">Depoimentos</h2>
        <div class="mt-6 grid gap-4 sm:mt-8 sm:gap-6 md:grid-cols-2">
            @foreach($testimonials as $item)
                <blockquote class="rounded-xl bg-white p-5 shadow-sm ring-1 ring-stone-200 sm:p-6">
                    <p class="text-sm leading-relaxed text-stone-700 sm:text-base">"{{ $item['text'] ?? '' }}"</p>
                    <footer class="mt-4 text-sm font-semibold text-brand-700">— {{ $item['name'] ?? '' }}</footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
