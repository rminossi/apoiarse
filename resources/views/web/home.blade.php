@extends('layouts.public')

@section('content')
<section class="bg-gradient-to-b from-brand-50 to-stone-50">
    <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 lg:px-8 lg:py-24">
        <div class="mx-auto max-w-3xl text-center">
            <h1 class="text-4xl font-extrabold tracking-tight text-stone-900 sm:text-5xl">
                Apoie causas que <span class="text-brand-600">transformam vidas</span>
            </h1>
            <p class="mt-4 text-lg text-stone-600">
                Crie campanhas, compartilhe com sua rede e arrecade com segurança via PIX ou cartão.
            </p>
            <form action="{{ route('web.campaigns') }}" method="GET" class="mt-8 flex gap-2">
                <input type="search" name="q" placeholder="Buscar campanhas..." class="input-field flex-1" value="{{ request('q') }}">
                <button type="submit" class="btn-primary">Buscar</button>
            </form>
            <div class="mt-6 flex flex-wrap justify-center gap-3">
                <a href="{{ route('web.campaigns') }}" class="btn-primary">Explorar campanhas</a>
                <a href="{{ route('usuario.campanhas.create') }}" class="btn-outline">Criar campanha</a>
            </div>
        </div>
    </div>
</section>

<section class="border-y border-stone-200 bg-white py-10">
    <div class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-4 sm:grid-cols-3 sm:px-6 lg:px-8">
        <div class="text-center">
            <p class="text-3xl font-bold text-brand-600">R$ {{ number_format($stats['total_raised'], 2, ',', '.') }}</p>
            <p class="mt-1 text-sm text-stone-500">Total arrecadado</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-brand-600">{{ $stats['active_campaigns'] }}</p>
            <p class="mt-1 text-sm text-stone-500">Campanhas ativas</p>
        </div>
        <div class="text-center">
            <p class="text-3xl font-bold text-brand-600">{{ $stats['supporters'] }}</p>
            <p class="mt-1 text-sm text-stone-500">Apoiadores</p>
        </div>
    </div>
</section>

@if($categories->isNotEmpty())
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="section-title text-center">Explore por categoria</h2>
        <div class="mt-8 grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
            @foreach($categories as $category)
                <a href="{{ route('web.campaigns', ['category' => $category->slug]) }}"
                   class="flex flex-col items-center rounded-xl bg-white p-6 shadow-sm ring-1 ring-stone-200 transition hover:shadow-md hover:ring-brand-300">
                    <span class="flex h-12 w-12 items-center justify-center rounded-full text-lg font-bold text-white"
                          style="background-color: {{ $category->color }}">{{ mb_substr($category->name, 0, 1) }}</span>
                    <span class="mt-3 text-sm font-semibold text-stone-800">{{ $category->name }}</span>
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($featuredCampaigns->isNotEmpty())
<section class="bg-brand-50 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="section-title">Em destaque</h2>
        <p class="section-subtitle">Campanhas selecionadas pela nossa equipe</p>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($featuredCampaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="section-title">Campanhas recentes</h2>
        @if($activeCampaigns->isEmpty())
            <x-empty-state title="Nenhuma campanha ativa" description="Seja o primeiro a criar uma campanha!" :action="route('usuario.campanhas.create')" class="mt-8" />
        @else
            <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
                @foreach($activeCampaigns as $campaign)
                    <x-campaign-card :campaign="$campaign" />
                @endforeach
            </div>
            <div class="mt-8 text-center">
                <a href="{{ route('web.campaigns') }}" class="btn-secondary">Ver todas</a>
            </div>
        @endif
    </div>
</section>

@if(!empty($howItWorks))
<section class="border-t border-stone-200 bg-white py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="section-title text-center">Como funciona</h2>
        <div class="mt-12 grid gap-8 md:grid-cols-3">
            @foreach($howItWorks as $step)
                <div class="text-center">
                    <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-brand-600 text-xl font-bold text-white">{{ $step['step'] ?? '' }}</div>
                    <h3 class="mt-4 text-lg font-semibold text-stone-900">{{ $step['title'] ?? '' }}</h3>
                    <p class="mt-2 text-sm text-stone-600">{{ $step['description'] ?? '' }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
@endif

@if($finishedCampaigns->isNotEmpty())
<section class="py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="section-title">Campanhas encerradas</h2>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($finishedCampaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
    </div>
</section>
@endif

@if(!empty($testimonials))
<section class="bg-stone-100 py-16">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <h2 class="section-title text-center">Depoimentos</h2>
        <div class="mt-8 grid gap-6 md:grid-cols-2">
            @foreach($testimonials as $item)
                <blockquote class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-stone-200">
                    <p class="text-stone-700">"{{ $item['text'] ?? '' }}"</p>
                    <footer class="mt-4 text-sm font-semibold text-brand-700">— {{ $item['name'] ?? '' }}</footer>
                </blockquote>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
