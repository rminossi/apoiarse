@extends('layouts.public')

@section('content')
<div class="page-container py-8 sm:py-10">
    <div class="flex flex-col items-center gap-6 sm:flex-row sm:items-start">
        <img src="{{ $creator->avatar_url }}" alt="{{ $creator->name }}" class="h-24 w-24 rounded-full ring-4 ring-brand-100">
        <div class="text-center sm:text-left">
            <h1 class="text-3xl font-bold text-stone-900">{{ $creator->name }}</h1>
            @if($creator->bio)
                <p class="mt-2 max-w-2xl text-stone-600">{{ $creator->bio }}</p>
            @endif
            <p class="mt-3 text-sm text-stone-500">
                {{ $creator->campaigns()->count() }} campanha(s) ·
                R$ {{ number_format($totalRaised, 2, ',', '.') }} arrecadado(s)
            </p>
        </div>
    </div>

    @if($activeCampaigns->isNotEmpty())
    <section class="mt-12">
        <h2 class="section-title">Campanhas ativas</h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($activeCampaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
    </section>
    @endif

    @if($finishedCampaigns->isNotEmpty())
    <section class="mt-12">
        <h2 class="section-title">Campanhas encerradas</h2>
        <div class="mt-6 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($finishedCampaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
    </section>
    @endif

    @if($activeCampaigns->isEmpty() && $finishedCampaigns->isEmpty())
        <x-empty-state title="Nenhuma campanha" description="Este criador ainda não possui campanhas publicadas." class="mt-12" />
    @endif
</div>
@endsection
