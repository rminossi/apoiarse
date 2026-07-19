<div class="dash_content_search">
    <div class="dash_content_search_close">
        <p class="text-right">
            <button class="btn btn-red icon-times icon-notext search_close"></button>
        </p>
    </div>

    <header>
        <h3 class="icon-search">Filtrar campanhas</h3>
    </header>

    <main>
        <form action="{{ route('admin.campaigns.index') }}" method="GET">
            <label>
                <span>Título:</span>
                <input type="text" name="q" value="{{ request('q') }}">
            </label>

            <label>
                <span>Categoria:</span>
                <select name="category_id">
                    <option value="">Todas</option>
                    @foreach($categories ?? [] as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $category->name }}</option>
                    @endforeach
                </select>
            </label>

            <label>
                <span>Status:</span>
                <select name="status">
                    <option value="">Todos</option>
                    <option value="1" @selected(request('status') == '1')>Ativo</option>
                    <option value="2" @selected(request('status') == '2')>Inativo</option>
                    <option value="3" @selected(request('status') == '3')>Encerrado</option>
                </select>
            </label>

            <label>
                <span>Criador:</span>
                <select name="user_id">
                    <option value="">Todos</option>
                    @foreach($users ?? [] as $u)
                        <option value="{{ $u->id }}" @selected(request('user_id') == $u->id)>{{ $u->name }}</option>
                    @endforeach
                </select>
            </label>

            <button class="btn btn-block btn-large btn-green icon-search">Filtrar</button>
        </form>
    </main>
</div>
