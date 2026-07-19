@php
    $navCategories = \Illuminate\Support\Facades\Cache::remember('categories.active', 3600, fn () => \App\Models\Category::active()->get());
@endphp

<header class="fixed inset-x-0 top-0 z-50 border-b border-stone-200 bg-white/95 backdrop-blur supports-[backdrop-filter]:bg-white/80" x-data="mobileNav">
    <div class="page-container flex items-center justify-between gap-3 py-3">
        <a href="{{ route('web.home') }}" class="shrink-0">
            <img src="{{ asset('assets/frontend/images/apoiarse_logo.png') }}" alt="Apoiar-se" class="h-9 w-auto max-w-[130px] sm:h-10 sm:max-w-[150px]">
        </a>

        <nav class="hidden items-center gap-5 lg:flex">
            <a href="{{ route('web.campaigns') }}" class="text-sm font-medium {{ request()->routeIs('web.campaigns') ? 'text-brand-600' : 'text-stone-600 hover:text-brand-600' }}">Campanhas</a>
            <div class="relative" x-data="{ open: false }">
                <button type="button" @click="open = !open" class="flex items-center gap-1 text-sm font-medium text-stone-600 hover:text-brand-600">
                    Categorias
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute left-0 z-50 mt-2 w-48 rounded-lg bg-white py-2 shadow-lg ring-1 ring-stone-200">
                    @foreach($navCategories as $cat)
                        <a href="{{ route('web.campaigns', ['category' => $cat->slug]) }}"
                           class="block px-4 py-2 text-sm text-stone-700 hover:bg-brand-50 hover:text-brand-700">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('web.sobre') }}" class="text-sm font-medium {{ request()->routeIs('web.sobre') ? 'text-brand-600' : 'text-stone-600 hover:text-brand-600' }}">Sobre</a>
            <a href="{{ route('web.contato') }}" class="text-sm font-medium {{ request()->routeIs('web.contato') ? 'text-brand-600' : 'text-stone-600 hover:text-brand-600' }}">Contato</a>
        </nav>

        <div class="hidden items-center gap-2 lg:flex">
            @auth
                <a href="{{ route('web.my-donations') }}" class="text-sm font-medium text-stone-600 hover:text-brand-600">Minhas doações</a>
                <a href="{{ route('usuario.campanhas.create') }}" class="btn-primary !px-4 !py-2">Criar campanha</a>
                <a href="{{ route('usuario.home') }}" class="btn-secondary !px-4 !py-2">Painel</a>
            @else
                <a href="{{ route('sessao.login') }}" class="btn-secondary !px-4 !py-2">Entrar</a>
                <a href="{{ route('usuario.campanhas.create') }}" class="btn-primary !px-4 !py-2">Criar campanha</a>
            @endauth
        </div>

        <button type="button" @click="toggle()" class="inline-flex min-h-11 min-w-11 items-center justify-center rounded-lg text-stone-700 ring-1 ring-stone-200 lg:hidden" :aria-expanded="open" aria-label="Abrir menu">
            <svg x-show="!open" class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
            <svg x-show="open" x-cloak class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>
    </div>

    <div x-show="open" x-transition.opacity x-cloak @click.outside="close()"
         class="border-t border-stone-200 bg-white lg:hidden">
        <nav class="page-container flex max-h-[calc(100dvh-4.5rem)] flex-col gap-1 overflow-y-auto py-4">
            <a href="{{ route('web.campaigns') }}" @click="close()" class="rounded-lg px-3 py-3 text-base font-medium text-stone-800 hover:bg-stone-100">Campanhas</a>
            <p class="px-3 pt-2 text-xs font-semibold uppercase tracking-wide text-stone-400">Categorias</p>
            @foreach($navCategories as $cat)
                <a href="{{ route('web.campaigns', ['category' => $cat->slug]) }}" @click="close()"
                   class="rounded-lg px-3 py-2.5 text-sm text-stone-700 hover:bg-stone-100">{{ $cat->name }}</a>
            @endforeach
            <div class="my-2 border-t border-stone-100"></div>
            <a href="{{ route('web.sobre') }}" @click="close()" class="rounded-lg px-3 py-3 text-base font-medium text-stone-800 hover:bg-stone-100">Sobre</a>
            <a href="{{ route('web.contato') }}" @click="close()" class="rounded-lg px-3 py-3 text-base font-medium text-stone-800 hover:bg-stone-100">Contato</a>
            @auth
                <a href="{{ route('web.my-donations') }}" @click="close()" class="rounded-lg px-3 py-3 text-base font-medium text-stone-800 hover:bg-stone-100">Minhas doações</a>
                <a href="{{ route('usuario.home') }}" @click="close()" class="rounded-lg px-3 py-3 text-base font-medium text-stone-800 hover:bg-stone-100">Painel</a>
            @else
                <a href="{{ route('sessao.login') }}" @click="close()" class="rounded-lg px-3 py-3 text-base font-medium text-stone-800 hover:bg-stone-100">Entrar</a>
            @endauth
            <a href="{{ route('usuario.campanhas.create') }}" @click="close()" class="btn-primary mt-3 w-full">Criar campanha</a>
        </nav>
    </div>
</header>
