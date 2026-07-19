@if ($paginator->hasPages())
    <nav role="navigation" aria-label="Paginação" class="flex flex-col items-center gap-3 sm:flex-row sm:justify-between">
        <p class="text-sm text-stone-600">
            Mostrando
            <span class="font-medium">{{ $paginator->firstItem() }}</span>
            a
            <span class="font-medium">{{ $paginator->lastItem() }}</span>
            de
            <span class="font-medium">{{ $paginator->total() }}</span>
        </p>

        <div class="flex w-full flex-wrap items-center justify-center gap-1 sm:w-auto">
            @if ($paginator->onFirstPage())
                <span class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm text-stone-400 ring-1 ring-stone-200">Anterior</span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-medium text-stone-700 ring-1 ring-stone-200 hover:bg-stone-50">Anterior</a>
            @endif

            @foreach ($elements as $element)
                @if (is_string($element))
                    <span class="px-2 text-stone-400">{{ $element }}</span>
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <span class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg bg-brand-600 px-3 text-sm font-semibold text-white">{{ $page }}</span>
                        @else
                            <a href="{{ $url }}" class="inline-flex min-h-10 min-w-10 items-center justify-center rounded-lg px-3 text-sm font-medium text-stone-700 ring-1 ring-stone-200 hover:bg-stone-50">{{ $page }}</a>
                        @endif
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm font-medium text-stone-700 ring-1 ring-stone-200 hover:bg-stone-50">Próxima</a>
            @else
                <span class="inline-flex min-h-10 items-center rounded-lg px-3 text-sm text-stone-400 ring-1 ring-stone-200">Próxima</span>
            @endif
        </div>
    </nav>
@endif
