@extends('layouts.public')

@section('content')
<div class="page-container py-6 sm:py-10">
    <div class="mb-6 sm:mb-8">
        <h1 class="section-title">Campanhas</h1>
        <p class="section-subtitle">Encontre causas para apoiar ou crie a sua</p>
    </div>

    <form method="GET" action="{{ route('web.campaigns') }}" class="mb-6 space-y-3 rounded-xl bg-white p-4 shadow-sm ring-1 ring-stone-200 sm:mb-8 sm:space-y-4">
        <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar campanhas..." class="input-field w-full">
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <select name="category" class="input-field w-full">
                <option value="">Todas categorias</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->slug }}" @selected(request('category') === $cat->slug)>{{ $cat->name }}</option>
                @endforeach
            </select>
            <select name="status" class="input-field w-full">
                <option value="active" @selected(request('status', 'active') === 'active')>Ativas</option>
                <option value="finished" @selected(request('status') === 'finished')>Encerradas</option>
                <option value="all" @selected(request('status') === 'all')>Todas</option>
            </select>
        </div>
        <div class="flex flex-col gap-3 sm:flex-row sm:flex-wrap sm:items-center">
            <select name="sort" class="input-field w-full sm:w-auto sm:min-w-[180px]">
                <option value="recent" @selected(request('sort', 'recent') === 'recent')>Mais recentes</option>
                <option value="supporters" @selected(request('sort') === 'supporters')>Mais apoiadas</option>
                <option value="almost_goal" @selected(request('sort') === 'almost_goal')>Quase na meta</option>
            </select>
            <button type="submit" class="btn-primary w-full sm:w-auto">Filtrar</button>
            @if(request()->hasAny(['q', 'category', 'status', 'sort']))
                <a href="{{ route('web.campaigns') }}" class="text-center text-sm text-stone-500 hover:text-brand-600 sm:text-left">Limpar filtros</a>
            @endif
        </div>
    </form>

    @if($campaigns->isEmpty())
        <x-empty-state title="Nenhuma campanha encontrada" description="Tente outros filtros ou crie uma nova campanha." :action="route('usuario.campanhas.create')" />
    @else
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 sm:gap-6 lg:grid-cols-3">
            @foreach($campaigns as $campaign)
                <x-campaign-card :campaign="$campaign" />
            @endforeach
        </div>
        @if($campaigns->hasPages())
            <div class="mt-8">{{ $campaigns->links('vendor.pagination.tailwind') }}</div>
        @endif
    @endif
</div>
@endsection
