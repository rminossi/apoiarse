@extends('admin.master.master')
@section('content')
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-table">Categorias</h2>
        <div class="dash_content_app_header_actions">
            <a href="{{ route('admin.categories.create') }}" class="btn btn-orange icon-plus">Nova categoria</a>
        </div>
    </header>

    <div class="dash_content_app_box">
        @if(session('message'))
            <div class="message message-green"><p>{{ session('message') }}</p></div>
        @endif
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Slug</th>
                    <th>Cor</th>
                    <th>Ordem</th>
                    <th>Ativa</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr>
                    <td>{{ $category->name }}</td>
                    <td>{{ $category->slug }}</td>
                    <td><span style="display:inline-block;width:20px;height:20px;background:{{ $category->color }};border-radius:4px;"></span></td>
                    <td>{{ $category->sort_order }}</td>
                    <td>{{ $category->is_active ? 'Sim' : 'Não' }}</td>
                    <td>
                        <a href="{{ route('admin.categories.edit', $category) }}" class="btn btn-blue btn-small">Editar</a>
                        <form action="{{ route('admin.categories.destroy', $category) }}" method="POST" style="display:inline" onsubmit="return confirm('Remover categoria?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-red btn-small">Excluir</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</section>
@endsection
