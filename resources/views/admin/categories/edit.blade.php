@extends('admin.master.master')
@section('content')
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-pencil">Editar categoria</h2>
    </header>
    <div class="dash_content_app_box">
        <form class="app_form" action="{{ route('admin.categories.update', $category) }}" method="POST">
            @csrf @method('PUT')
            @include('admin.categories.partials.form')
            <button type="submit" class="btn btn-green">Atualizar</button>
        </form>
    </div>
</section>
@endsection
