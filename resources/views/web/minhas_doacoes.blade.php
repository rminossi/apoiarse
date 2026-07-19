@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
    <h1 class="section-title">Minhas doações</h1>
    <p class="section-subtitle">Histórico das campanhas que você apoiou</p>

    @if($donations->isEmpty())
        <x-empty-state title="Nenhuma doação ainda" description="Explore campanhas e faça sua primeira doação!" :action="route('web.campaigns')" actionLabel="Explorar campanhas" class="mt-8" />
    @else
        <div class="mt-8 space-y-4">
            @foreach($donations as $donation)
                <div class="flex flex-col gap-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        @if($donation->campaign)
                            <a href="{{ route('web.campaign', $donation->campaign->slug) }}" class="font-semibold text-stone-900 hover:text-brand-600">
                                {{ $donation->campaign->title }}
                            </a>
                        @endif
                        <p class="text-sm text-stone-500">{{ $donation->created_at->format('d/m/Y H:i') }} · {{ $donation->payment_method ?? 'PIX' }}</p>
                    </div>
                    <span class="text-lg font-bold text-brand-700">R$ {{ number_format($donation->amount, 2, ',', '.') }}</span>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection
