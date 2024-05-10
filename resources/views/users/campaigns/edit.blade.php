@extends('users.master.master')
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
                        <li><a href="" class="text-orange">Editar Campanha</a></li>
                    </ul>
                </nav>
            </div>
        </header>
        @include('users.campaigns.filter')
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
                <ul class="nav_tabs" style="flex-flow: row">
                    <li class="nav_tabs_item">
                        <a href="#data" class="nav_tabs_item_link active">Dados da Campanha</a>
                    </li>
                    <li class="nav_tabs_item">
                        <a href="#photos" class="nav_tabs_item_link">Fotos</a>
                    </li>
                    <li class="nav_tabs_item">
                        <a href="#donations" class="nav_tabs_item_link">Doações</a>
                    </li>
                </ul>
                <form class="app_form" action="{{route('usuario.campanhas.update', ['campanha' => $campaign->id])}}"
                      method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="nav_tabs_content">
                        <div id="data">
                            <label class="label">
                                <span class="legend">*Título Site:</span>
                                <input type="text" name="title" placeholder="Insira o título da campanha"
                                       value="{{old('title') ?? $campaign->title}}"/>
                            </label>
                            <div class="label">
                                <label class="label">
                                    <span class="legend">*Descrição da Campanha:</span>
                                    <textarea name="description" cols="30" rows="10"
                                              id="mytextarea">{{old('description') ?? $campaign->description}}</textarea>
                                </label>
                            </div>
                            <div class="label_g3">
                                <label class="label">
                                    <span class="legend">*Tipo:</span>
                                    <select name="type">
                                        <option {{ $campaign->type == 'sick' ? 'selected' : '' }} value="sick">Doença
                                        </option>
                                        <option
                                            {{ $campaign->type == 'residential-accident' ? 'selected' : '' }} value="residential-accident">
                                            Acidente Residencial
                                        </option>
                                        <option
                                            {{ $campaign->type == 'public-calamity' ? 'selected' : '' }} value="public-calamity">
                                            Calamidade Pública
                                        </option>
                                        <option {{ $campaign->type == 'other' ? 'selected' : '' }} value="other">Outro
                                        </option>
                                    </select>
                                </label>
                            </div>
                            <div class="label_g2">
                                <label class="label mr-2">
                                    <span class="legend">Meta (R$) - deixar em branco para sem limite:</span>
                                    <input type="text" class="mask-money" name="goal" placeholder="Ex: R$ 1.000,00"
                                           value="{{old('goal') ?? $campaign->goal}}"/>
                                </label>
                                <label class="label">
                                    <span class="legend">*Status:</span>
                                    <select name="status">
                                        <option value="1" {{ (old('status') == '1' ? 'selected' : '') }}>Ativo</option>
                                        <option value="2" {{ (old('status') == '2' ? 'selected' : '') }}>Inativo
                                        </option>
                                        <option value="3" {{ (old('status') == '3' ? 'selected' : '') }}>Encerrado
                                        </option>
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
                                    @foreach($campaign->images()->get() as $image)
                                        <div class="property_image_item">
                                            <img style="object-fit: cover; width: 300px; height: 300px"
                                                 src="{{url('storage/'.$image->image->path)}}">
                                            <div class="property_image_actions">
                                                <a href="javascript:void(0)"
                                                   class="btn btn-small {{ ($image->image->cover == true ? 'btn-green' : '') }} icon-check icon-notext image-set-cover"
                                                   data-action="{{ route('usuario.campaigns.imageSetCover', ['image' => $image->image->id]) }}"></a>
                                                <a href="javascript:void(0)"
                                                   class="btn btn-red btn-small icon-times icon-notext image-remove"
                                                   data-action="{{ route('usuario.campaigns.removeImage', ['image' => $image->image->id]) }}"></a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div id="donations" class="d-none">
                                    <div style="display: block;
    overflow-x: auto;
    white-space: nowrap;">
                                    <table id="dataTable" class="nowrap stripe" style="width: 100%">
                                        <thead>
                                        <tr>
                                            <th>Situação</th>
                                            <th>Nome Completo</th>
                                            <th>Valor</th>
                                            <th>Data</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($donations as $donation)
                                            <tr>
                                                <td>
                                                    @if($donation->status == 1)
                                                        Pendente
                                                    @elseif($donation->status == 2)
                                                        Expirado
                                                    @else
                                                        Aprovado
                                                    @endif
                                                </td>
                                                <td>{{$donation->user->name}}</td>
                                                <td>R$ {{number_format($donation->amount, 2, ',', '.')}}</td>
                                                <td>{{date('d/m/Y H:i', strtotime($donation->created_at))}}</td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                        </div>
                    </div>
                    <div class="text-right mt-2">
                        <button class="btn btn-large btn-green icon-check-square-o" type="submit">Salvar alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <script>
        CKEDITOR.replace('description', {
            toolbar: [
                {
                    name: 'document',
                    groups: ['mode', 'document', 'doctools'],
                    items: ['Source', '-', 'Save', 'NewPage', 'Preview', 'Print', '-', 'Templates']
                },
                {
                    name: 'clipboard',
                    groups: ['clipboard', 'undo'],
                    items: ['Cut', 'Copy', 'Paste', 'PasteText', 'PasteFromWord', '-', 'Undo', 'Redo']
                },
                {
                    name: 'editing',
                    groups: ['find', 'selection', 'spellchecker'],
                    items: ['Find', 'Replace', '-', 'SelectAll', '-', 'Scayt']
                },
                {
                    name: 'forms',
                    items: ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField']
                },
                '/',
                {
                    name: 'basicstyles',
                    groups: ['basicstyles', 'cleanup'],
                    items: ['Bold', 'Italic', 'Underline', 'Strike', 'Subscript', 'Superscript', '-', 'RemoveFormat']
                },
                {
                    name: 'paragraph',
                    groups: ['list', 'indent', 'blocks', 'align', 'bidi'],
                    items: ['NumberedList', 'BulletedList', '-', 'Outdent', 'Indent', '-', 'Blockquote', 'CreateDiv', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', '-', 'BidiLtr', 'BidiRtl', 'Language']
                },
                {name: 'links', items: ['Link', 'Unlink', 'Anchor']},
                '/',
                {name: 'styles', items: ['Styles', 'Format', 'Font', 'FontSize']},
                {name: 'colors', items: ['TextColor', 'BGColor']},
                {name: 'tools', items: ['Maximize', 'ShowBlocks']},
                {name: 'others', items: ['-']},
                {name: 'about', items: ['About']}
            ]
        });
    </script>
    <script>
        $(function () {
            if ($('#modality').val() == 3) {
                $('#fixed-donations').removeClass('d-none');
            }

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

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

            $('.image-set-cover').click(function (event) {
                event.preventDefault();

                var button = $(this);

                $.post(button.data('action'), {}, function (response) {
                    if (response.success === true) {
                        $('.property_image').find('a.btn-green').removeClass('btn-green');
                        button.addClass('btn-green');
                    }
                }, 'json');
            });

            $('.image-remove').click(function (event) {
                event.preventDefault();

                var button = $(this);
                $.ajax({
                    url: button.data('action'),
                    type: 'DELETE',
                    dataType: 'json',
                    success: function (response) {
                        if (response.success === true) {
                            button.closest('.property_image_item').fadeOut(function () {
                                $(this).remove();
                            });
                        }
                    }
                })
            });
        });
    </script>
@endsection
