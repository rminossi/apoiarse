@extends('admin.master.master')
@section('content')
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2 class="icon-plus">Nova categoria</h2>
    </header>
    <div class="dash_content_app_box">
        <form class="app_form" action="{{ route('admin.categories.store') }}" method="POST">
            @csrf
            @include('admin.categories.partials.form')
            <button type="submit" class="btn btn-green">Salvar</button>
        </form>
    </div>
</section>
@endsection
