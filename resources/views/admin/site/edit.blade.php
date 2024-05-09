@extends('admin.master.master')
@section('content')
    <section class="dash_content_app">

        <header class="dash_content_app_header">
            <h2 class="icon-search">Editar Campanha</h2>

            <div class="dash_content_app_header_actions">
                <nav class="dash_content_app_breadcrumb">
                    <ul>
                        <li><a href="">Dashboard</a></li>
                        <li class="separator icon-angle-right icon-notext"></li>
                        <li><a href="">Campanha</a></li>
                        <li class="separator icon-angle-right icon-notext"></li>
                        <li><a href="" class="text-orange">Cadastrar Campanha</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <div class="dash_content_app_box">

            <div class="nav">

                @if($errors->all())
                    @foreach($errors->all() as $error)
                        <div class="message message-red">
                            <p class="icon-asterisk">{{ $error }}</p>
                        </div>
                    @endforeach
                @endif
                @if(session()->exists('message'))
                    <div class="message message-green">
                        <p class="icon-asterisk">{{ session()->get('message') }}</p>
                    </div>
                @endif
                <ul class="nav_tabs">
                    <li class="nav_tabs_item">
                        <a href="#data" class="nav_tabs_item_link active">Dados do Site</a>
                    </li>
                </ul>
                <form class="app_form" action="{{route('admin.site.update', ['site' => '1'])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('put')
                    <div class="nav_tabs_content">
                        <div id="data">
                            <div class="label_g2">
                                <label class="label">
                                    <span class="legend">*Telefone:</span>
                                    <input type="text" name="phone" placeholder="Ex: (51) 99999-9999"
                                           value="{{old('phone') ?? ($site->phone ?? '')}}"/>
                                </label>
                                <label class="label mr-2">
                                    <span class="legend">*Email:</span>
                                    <input type="text" name="email" placeholder="Ex: contato@teste.com.br"
                                           value="{{old('email') ?? $site->email ?? ''}}"/>
                                </label>
                                <label class="label">
                                    <span class="legend">Grupo Whatsapp:</span>
                                    <input type="text" name="whatsapp_group" placeholder="Ex: https://chat.whatsapp.com/Jfm4I5vhBHAKGtXdkuPROp"
                                           value="{{old('whatsapp_group') ?? $site->whatsapp_group ?? ''}}"/>
                                </label>
                            </div>
                            <hr class="mb-1"/>
                            <h5 class="text-center my-2 text-muted">Dados Bancários</h5>
                            <div class="label_g2">
                                <label class="label">
                                    <span class="legend">*Banco:</span>
                                    <input type="text" name="bank" placeholder="Ex: Bradesco"
                                           value="{{old('bank') ?? $site->bank ?? ''}}"/>
                                </label>
                                <label class="label mr-2">
                                    <span class="legend">*Tipo da Conta:</span>
                                    <input type="text" name="type" placeholder="Ex: Conta Corrente"
                                           value="{{old('type') ?? $site->type ?? ''}}"/>
                                </label>
                                <label class="label mr-2">
                                    <span class="legend">*Conta Corrente:</span>
                                    <input type="text" name="acc" placeholder="1004765-9"
                                           value="{{old('acc') ?? $site->acc ?? ''}}"/>
                                </label>
                                <label class="label">
                                    <span class="legend">Agência:</span>
                                    <input type="text" name="ag" placeholder="Ex: 1234"
                                           value="{{old('ag') ?? $site->ag ?? ''}}"/>
                                </label>
                            </div>
                            <div class="label_g2">
                                <label class="label">
                                    <span class="legend">Nome Completo:</span>
                                    <input type="text" name="fullName" placeholder="João da Silva"
                                           value="{{old('fullName') ?? $site->fullName ?? ''}}"/>
                                </label>
                                <label class="label">
                                    <span class="legend">CPF:</span>
                                    <input type="text" name="cpf" placeholder="Ex: 123456789-09"
                                           value="{{old('cpf') ?? $site->cpf ?? ''}}"/>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button class="btn btn-large btn-green icon-check-square-o" type="submit">Salvar alterações</button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        $(function () {
            $('input[name="files[]"]').change(function (files) {

                $('.content_image').text('');

                $.each(files.target.files, function (key, value) {
                    var reader = new FileReader();
                    reader.onload = function (value) {
                        $('.content_image').append(
                            '<div class="property_image_item">' +
                            '<div class="embed radius" ' +
                            'style="background-image: url(' + value.target.result + '); background-size: cover; background-position: center center;">' +
                            '</div>' +
                            '</div>');
                    };
                    reader.readAsDataURL(value);
                });
            });
        });
    </script>
@endsection
