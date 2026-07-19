<header class="fixed inset-x-0 top-0 z-40 border-b border-stone-200 bg-white/95 backdrop-blur" x-data="mobileNav">
    <div class="mx-auto flex max-w-7xl items-center justify-between px-4 py-3 sm:px-6 lg:px-8">
        <a href="{{ route('web.home') }}" class="flex items-center gap-2">
            <img src="{{ asset('assets/frontend/images/apoiarse_logo.png') }}" alt="Apoiar-se" class="h-10 w-auto">
        </a>

        <nav class="hidden items-center gap-6 md:flex">
            <a href="{{ route('web.campaigns') }}" class="text-sm font-medium {{ request()->routeIs('web.campaigns') ? 'text-brand-600' : 'text-stone-600 hover:text-brand-600' }}">Campanhas</a>
            <div class="relative" x-data="{ open: false }">
                <button @click="open = !open" class="flex items-center gap-1 text-sm font-medium text-stone-600 hover:text-brand-600">
                    Categorias
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                </button>
                <div x-show="open" @click.outside="open = false" x-cloak
                     class="absolute left-0 mt-2 w-48 rounded-lg bg-white py-2 shadow-lg ring-1 ring-stone-200">
                    @foreach(\App\Models\Category::active()->get() as $cat)
                        <a href="{{ route('web.campaigns', ['category' => $cat->slug]) }}"
                           class="block px-4 py-2 text-sm text-stone-700 hover:bg-brand-50 hover:text-brand-700">{{ $cat->name }}</a>
                    @endforeach
                </div>
            </div>
            <a href="{{ route('web.sobre') }}" class="text-sm font-medium {{ request()->routeIs('web.sobre') ? 'text-brand-600' : 'text-stone-600 hover:text-brand-600' }}">Sobre</a>
            <a href="{{ route('web.contato') }}" class="text-sm font-medium {{ request()->routeIs('web.contato') ? 'text-brand-600' : 'text-stone-600 hover:text-brand-600' }}">Contato</a>
        </nav>

        <div class="hidden items-center gap-3 md:flex">
            @auth
                <a href="{{ route('web.my-donations') }}" class="text-sm font-medium text-stone-600 hover:text-brand-600">Minhas doações</a>
                <a href="{{ route('usuario.campanhas.create') }}" class="btn-primary">Criar campanha</a>
                <a href="{{ route('usuario.home') }}" class="btn-secondary">Painel</a>
            @else
                <a href="{{ route('sessao.login') }}" class="btn-secondary">Entrar</a>
                <a href="{{ route('usuario.campanhas.create') }}" class="btn-primary">Criar campanha</a>
            @endauth
        </div>

        <button @click="toggle()" class="rounded-lg p-2 text-stone-600 md:hidden" aria-label="Menu">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
    </div>

    <div x-show="open" x-transition @click.outside="close()" x-cloak class="border-t border-stone-200 bg-white md:hidden">
        <nav class="flex flex-col gap-1 px-4 py-4">
            <a href="{{ route('web.campaigns') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">Campanhas</a>
            <a href="{{ route('web.sobre') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">Sobre</a>
            <a href="{{ route('web.contato') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">Contato</a>
            @auth
                <a href="{{ route('web.my-donations') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">Minhas doações</a>
                <a href="{{ route('usuario.home') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">Painel</a>
            @else
                <a href="{{ route('sessao.login') }}" class="rounded-lg px-3 py-2 text-sm font-medium text-stone-700 hover:bg-stone-100">Entrar</a>
            @endauth
            <a href="{{ route('usuario.campanhas.create') }}" class="btn-primary mt-2">Criar campanha</a>
        </nav>
    </div>
</header>
