@extends('layouts.public')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="mb-8">
        <h1 class="section-title">Campanhas</h1>
        <p class="section-subtitle">Encontre causas para apoiar ou crie a sua</p>
    </div>

    <form method="GET" action="{{ route('web.campaigns') }}" class="mb-8 space-y-4 rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200">
        <div class="grid gap-4 md:grid-cols-4">
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar..." class="input-field md:col-span-2">
            <select name="category" class="input-field">
                <option value="">Todas categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="input-field">
                <option value="active" @selected(request('status', 'active') === 'active')>Ativas</option>
                <option value="finished" @selected(request('status') === 'finished')>Encerradas</option>
                <option value="all" @selected(request('status') === 'all')>Todas</option>
            </select>
        </div>
        <div class="flex flex-wrap items-center gap-4">
            <select name="sort" class="input-field w-auto">
                <option value="recent" @selected(request('sort', 'recent') === 'recent')>Mais recentes</option>
                <option value="supporters" @selected(request('sort') === 'supporters')>Mais apoiadas</option>
                <option value="almost_goal" @selected(request('sort') === 'almost_goal')>Quase na meta</option>
            </select>
            <button type="submit" class="btn-primary">Filtrar</button>
            @if(request()->hasAny(['q', 'category', 'status', 'sort']))
                <a href="{{ route('web.campaigns') }}" class="text-sm text-stone-500 hover:text-brand-600">Limpar filtros</a>
            @endif
        </div>
    </form>

    @if($campaigns->isEmpty())
        <x-empty-state title="Nenhuma campanha encontrada" description="Tente outros filtros ou crie uma nova campanha." :action="route('usuario.campanhas.create')" />
    @else
        <div class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            @foreach($campaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
        <div class="mt-8">{{ $campaigns->links() }}</div>
    @endif
</div>
@endsection
