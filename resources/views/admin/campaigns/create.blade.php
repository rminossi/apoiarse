@extends('admin.master.master')
@section('content')
    <section class="dash_content_app">

        <header class="dash_content_app_header">
            <h2 class="icon-search">Cadastrar Campanha</h2>

            <div class="dash_content_app_header_actions">
                <nav class="dash_content_app_breadcrumb">
                    <ul>
                        <li><a href="">Dashboard</a></li>
                        <li class="separator icon-angle-right icon-notext"></li>
                        <li><a href="">Campanhas</a></li>
                        <li class="separator icon-angle-right icon-notext"></li>
                        <li><a href="" class="text-orange">Cadastrar Campanha</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        @include('admin.campaigns.filter')

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
                        <a href="#data" class="nav_tabs_item_link active">Dados da Campanha</a>
                    </li>
                    <li class="nav_tabs_item">
                        <a href="#photos" class="nav_tabs_item_link">Imagens</a>
                    </li>
                </ul>
                <form class="app_form" action="{{route('admin.campaigns.store')}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="nav_tabs_content">
                        <div id="data">
                            <label class="label">
                                <span class="legend">*Título Site:</span>
                                <input type="text" name="title" placeholder="Insira o título da campanha" value="{{old('name')}}"/>
                            </label>
                            <div class="label">
                                <label class="label">
                                    <span class="legend">*Descrição da Campanha:</span>
                                    <textarea name="description" cols="30" rows="10" class="mce">{{ old('description') }}</textarea>
                                </label>
                            </div>
                            <div class="label_g2">
                                <label class="label">
                                    <span class="legend">*Tipo:</span>
                                    <select name="type">
                                        <option {{ (old('type') == 'sick' ? 'selected' : '') }} value="sick">Doença</option>
                                        <option {{ (old('type') == 'residential-accident' ? 'selected' : '') }} value="residential-accident">Acidente Residencial</option>
                                        <option {{ (old('type') == 'public-calamity' ? 'selected' : '') }} value="public-calamity">Calamidade Pública</option>
                                        <option {{ (old('type') == 'other' ? 'selected' : '') }} value="other">Outro</option>
                                    </select>
                                </label>
                            </div>
                            <div class="label_g2">
                                <label class="label mr-2">
                                    <span class="legend">Meta:</span>
                                    <input type="text" name="goal" placeholder="Ex: R$ 1.000,00"
                                           value="{{old('goal')}}"/>
                                </label>
                                <label class="label">
                                    <span class="legend">*Status:</span>
                                    <select name="status">
                                        <option value="1" {{ (old('status') == '1' ? 'selected' : '') }}>Ativo</option>
                                        <option value="2" {{ (old('status') == '2' ? 'selected' : '') }}>Inativo</option>
                                        <option value="3" {{ (old('status') == '3' ? 'selected' : '') }}>Encerrado</option>
                                    </select>
                                </label>
                            </div>
                        </div>
                        <div id="photos" class="d-none">
                            <div id="images">
                                <label class="label">
                                    <span class="legend">Imagens</span>
                                    <input type="file" name="files[]" multiple>
                                </label>
                                <div class="content_image"></div>
                                <div class="property_image">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button class="btn btn-large btn-green icon-check-square-o" type="submit">Criar Campanha
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        CKEDITOR.replace( 'description' );
    </script>
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
                            'style="background-image: url(' + value.target.result + ') background-size: cover; background-position: center center;">' +
                            '</div>' +
                            '</div>');
                    };
                    reader.readAsDataURL(value);
                });
            });
        });
    </script>

@endsection
