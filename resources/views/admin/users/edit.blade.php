@extends('admin.master.master')
@section('content')
<section class="dash_content_app">
    <header class="dash_content_app_header">
        <h2>Editar usuário</h2>
    </header>
    <div class="dash_content_app_box">
        <form class="app_form" method="POST" action="{{ route('admin.users.update', $user) }}">
            @csrf
            @method('PUT')
            <label class="label"><span class="legend">Nome</span><input type="text" name="name" value="{{ old('name', $user->name) }}" required></label>
            <label class="label"><span class="legend">E-mail</span><input type="email" name="email" value="{{ old('email', $user->email) }}" required></label>
            <label class="label"><span class="legend">CPF</span><input type="text" name="cpf" value="{{ old('cpf', $user->cpf) }}" required></label>
            <label class="label"><span class="legend">Telefone</span><input type="text" name="phone" value="{{ old('phone', $user->phone) }}"></label>
            <label class="label"><span class="legend">Wallet Asaas (split)</span><input type="text" name="asaas_wallet_id" value="{{ old('asaas_wallet_id', $user->asaas_wallet_id) }}"></label>
            <label class="label"><span class="legend">Nova senha</span><input type="password" name="password"></label>
            <label class="label"><span class="legend">Confirmar senha</span><input type="password" name="password_confirmation"></label>
            <label class="label"><input type="checkbox" name="is_admin" value="1" @checked(old('is_admin', $user->is_admin))> Administrador</label>
            <button type="submit" class="btn btn-green mt-2">Salvar</button>
        </form>
    </div>
</section>
@endsection
