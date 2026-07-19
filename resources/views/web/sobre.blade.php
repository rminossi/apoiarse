@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <h1 class="section-title">Sobre o Apoiar-se</h1>
    <p class="section-subtitle">Plataforma brasileira de crowdfunding para causas sociais, projetos pessoais e emergências.</p>

    @if(!empty($howItWorks))
    <section class="mt-12">
        <h2 class="text-xl font-semibold text-stone-900">Como funciona</h2>
        <div class="mt-6 space-y-4">
            @foreach($howItWorks as $step)
                <div class="flex gap-4 rounded-lg bg-white p-4 ring-1 ring-stone-200">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-brand-600 text-sm font-bold text-white">{{ $step['step'] ?? '' }}</span>
                    <div>
                        <h3 class="font-semibold">{{ $step['title'] ?? '' }}</h3>
                        <p class="text-sm text-stone-600">{{ $step['description'] ?? '' }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif

    @if(!empty($faq))
    <section class="mt-12" x-data="{ open: null }">
        <h2 class="text-xl font-semibold text-stone-900">Perguntas frequentes</h2>
        <div class="mt-6 space-y-2">
            @foreach($faq as $index => $item)
                <div class="rounded-lg bg-white ring-1 ring-stone-200">
                    <button @click="open = open === {{ $index }} ? null : {{ $index }}"
                            class="flex w-full items-center justify-between px-4 py-4 text-left font-medium text-stone-900">
                        {{ $item['question'] ?? '' }}
                        <svg class="h-5 w-5 transition" :class="open === {{ $index }} ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open === {{ $index }}" x-collapse x-cloak class="border-t border-stone-100 px-4 pb-4 pt-2 text-sm text-stone-600">
                        {{ $item['answer'] ?? '' }}
                    </div>
                </div>
            @endforeach
        </div>
    </section>
    @endif
</div>
@endsection
