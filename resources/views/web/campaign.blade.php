@extends('web.master.master')
@section('content')
    @if(!empty($campaign))
        <style>
            /* Modal Header */
            .modal-header {
                padding: 10px 16px;
                background-color: #fefefe;
                border: none;
            }

            /* Modal Body */
            .modal-body {
                padding: 35px 140px;
            }

            @media (max-width: 800px) {
                .modal-body {
                    padding: 35px 50px;
                }
            }

            /* Modal Footer */
            .modal-footer {
                padding: 2px 16px;
                background-color: #fefefe;
                border: none;
            }

            /* Modal Content */
            .modal-content {
                position: relative;
                background-color: #fefefe;
                margin: auto;
                margin-top: 50px !important;
                padding: 0;
                border: 1px solid #888;
                width: 80%;
                box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                animation-name: animatetop;
                animation-duration: 0.4s;
            }

            /* Add Animation */
            @keyframes animatetop {
                from {
                    top: -300px;
                    opacity: 0
                }

                to {
                    top: 0;
                    opacity: 1
                }
            }
        </style>
        <div class="fh5co-hero fh5co-hero-2">
            <div class="fh5co-overlay"></div>
            <div class="fh5co-cover fh5co-cover_2 text-center" data-stellar-background-ratio="0.5"
                 style="background-image: url({{url($campaign->cover())}}); background-position-y: center;">
                <div class="desc animate-box">
                    <h2>{{$campaign->title}}</h2>
                    <span>{{$campaign->model}}</span>
                    <span><a class="btn btn-primary btn-lg" href="#apoiar">Apoiar essa campanha</a></span>
                </div>
            </div>
        </div>
        <!-- end:header-top -->

        <div id="fh5co-services-section" class="fh5co-section-gray">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 heading-section animate-box">
                        <h3>{{$campaign->title}}</h3>
                        <p>{!!html_entity_decode($campaign->description)!!}</p>
                        <div class="row text-center justify-content-center">
                            <span><a class="btn btn-primary btn-lg" href="#apoiar">Apoiar essa campanha</a></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row text-center">
                    @foreach($campaign->images()->get() as $image)
                        <div class="col-md-4 col-sm-4">
                            <img style="object-fit: cover; width: 100%; height: 250px; margin-bottom: 30px"
                                 src="{{url("storage/" . $image->image->path)}}">
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        <div style="padding: 40px" id="fh5co-services">
            <div class="container" id="apoiar">
                <div class="row">
                    <div class="col-12 col text-center" id="cotas-aleatorias">
                        <h4 class="fh5co-number">Apoiar!</h4>
                        <div class="mt-3">
                            <div class="col text-center">
                                <p class="text-center h2" style="margin-top: 10px;margin-bottom: 10px">
                                    Apoiar essa campanha
                                </p>
                                <span class="text-center h3">Doe o quanto você puder, mas não deixe de doar!!</span>
                            </div>
                            @csrf
                            <input type="hidden" name="campaign_id" value="{{$campaign->id}}">
                            <input type="hidden" name="user_id" id="user_id">
                            <div class="row d-flex justify-content-center mt-3">
                                <label class="col-12 p-5 mt-3 h3 text-center">Valor do apoio</label>
                                <input class="col-4 p-4 mt-3 text-center h3" id="amount" name="amount" value="0,00">
                            </div>
                            <div class="row d-flex justify-content-center mt-3">
                                <div class="row col-12 d-flex justify-content-center my-3">
                                    <button class="col-md-2 btn btn-primary p-5 mt-3 btnDoar"
                                            type="button"> Apoiar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="loginModal" class="modal" style="position: fixed;">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="close">&times;</span>
                    <h3 class="modal-title text-center"></h3>
                </div>
                <div id="newUser">
                    <form method="POST" action="/doar">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="campaign_id" value="{{$campaign->id}}">
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="hidden" name="amount" id="amount">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="cpf" id="cpf" required class="form-control"
                                           placeholder="CPF">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" disabled name="name" id="name" required class="form-control"
                                           placeholder="Nome">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" disabled name="phone" id="phone" required class="form-control"
                                           placeholder="Telefone">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="email" disabled name="email" id="email" required class="form-control"
                                           placeholder="Email">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="password" name="password" id="password" required class="form-control"
                                           placeholder="Senha">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="password" name="password_confirmation" id="password_confirmation"
                                           required class="form-control" placeholder="Confirme a senha">
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <input type="submit" disabled id="doar" value="Enviar"
                                           class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div id="oldUser" style="display: none">
                    <form method="POST" action="/doar">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" name="campaign_id" value="{{$campaign->id}}">
                            <input type="hidden" name="user_id" id="user_id">
                            <input type="hidden" name="amount" id="amount">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <input type="text" name="email" required class="form-control"
                                           placeholder="Email">
                                </div>
                            </div>
                            <div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <input type="password" name="password" id="password" required
                                               class="form-control"
                                               placeholder="Senha">
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 text-center">
                                <div class="form-group">
                                    <input type="submit" disabled id="doar" value="Enviar"
                                           class="btn btn-primary">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                @if(request('confirmed'))
                    <div id="confirmationModal" class="modal" style="display:block">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h3 class="modal-title text-center">{{request('confirmed') === 'confirmar' ? "Reserva Efetuada!" : "Oooops, ocorreu um problema :("}}</h3>
                            </div>
                            <div class="modal-body">
                                @if (request('confirmed') === 'confirmar')
                                    <div class="row text-center">
                                        <h4 style="margin-bottom: 15px">Reserva efetuada com sucesso, seguem abaixo os
                                            dados
                                            para pagamento via pix do valor referente à(s) cotas.</h4>
                                        <h5 class="col-12 py-3">Lembramos que as reservas não pagas em até 1 dias, serão
                                            canceladas.</h5>
                                        <h4 class="col-12 py-3">Após o pagamento, confira os seus números na página
                                            "Meus
                                            Números", informando seu CPF para consulta.</h4>
                                    </div>
                                    <div class="row">
                                        <!-- show qrcode -->
                                        <div class="col-md-12">
                                            <div class="qrcode-container">
                                                <div class="col-12 text-center">
                                                    <img
                                                        src="data:image/png;base64, {{request('qrCode')['encodedImage']}}"
                                                        style="width: inherit;max-width: 300px"/>
                                                    <!-- button to copy value to clipboard -->
                                                    <input type="hidden" id="qrcode-value"
                                                           value="{{ isset(request('qrCode')['qrCode']) ? request('qrCode')['qrCode'] : request('qrCode')['payload']}}">
                                                    <button class="btn btn-primary col-12" id="qrcode-copy-button">
                                                        Copiar
                                                        Código
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                                @if (request('confirmed') === 'erro')
                                    <div class="row text-center">
                                        <h4 style="margin-bottom: 10px">Apoio não efetuado, verifique a campanha
                                            selecionada e
                                            os dados informados e tente novamente. Caso o erro persista, entre em
                                            contato
                                            conosco.</h4>
                                    </div>
                                @endif
                            </div>
                            <div class="modal-footer">
                                <div class="col-md-12 text-center">
                                    <div class="form-group">
                                        <a type="button" href="#cotas" id="fecharModal"
                                           class="btn btn-primary">Fechar</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>
        </div>
        <script>
            $(function ($) {
                var $Cpf = $("input[name=cpf]");
                $Cpf.mask('000.000.000-00', {
                    reverse: true
                });
                var $phone = $("input[name=phone]");
                $phone.mask("00 00000-0000", {
                    reverse: true
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                $('.btnDoar').on('click', function (e) {
                    $("#user_id").val(null);
                    $("input[name=cpf]").val(null);
                    $("input[name=name]").val(null);
                    $("input[name=phone]").val(null);
                    $("input[name=email]").val(null);
                    $('#loginModal').modal('show');
                    var amount = $("input[name=amount]").val();
                    $("[name=amount]").val(amount);
                    $(".modal-title").text("Apoio de R$ " + amount);
                    $('.modal-backdrop').remove();
                });

                $('.close').on('click', function (e) {
                    $('#reserveModal').modal('hide');
                });

                $('#fecharModal').on('click', function (e) {
                    $('#confirmationModal').css("display", "none");
                });

                $('input[name=cpf]').keyup(function () {
                    console.log('aqui');
                    var $Cpf = new Array();
                    $("input[name=cpf]").each(function () {
                        $Cpf.push($(this).val());
                    });
                    $Cpf = $Cpf.join('');
                    if ($Cpf.length === 14) {
                        $Cpf = $Cpf.replace(/[^\w\s]/gi, '')
                        $.get(`/getUserByCpf/${$Cpf}`, {}, function (response) {
                            if (Object.keys(response).length > 0) {
                                $("input[name=name]").val(response[0].fullName);
                                $("input[name=phone]").val(response[0].phone);
                                $("input[name=email]").val(response[0].email);
                                $("[name=user_id]").val(response[0].id);
                                $("input[name=cpf]").val($Cpf);
                                $("#doar").prop("disabled", false);
                                $("#newUser").css("display", "none");
                                $("#oldUser").css("display", "block");
                                $("input[name=name]").prop("disabled", false);
                                $("input[name=phone]").prop("disabled", false);
                                $("input[name=email]").prop("disabled", false);
                                //put $customer (php variable) to not null
                                $("#user_id").val(response[0].id);
                            } else {
                                $("input[name=name]").prop("disabled", false);
                                $("input[name=phone]").prop("disabled", false);
                                $("input[name=email]").prop("disabled", false);
                                $("#doar").prop("disabled", false);
                                $("#user_id").val(null);
                                $("input[name=name]").val(null);
                                $("input[name=phone]").val(null);
                                $("input[name=email]").val(null);
                            }
                        }, 'json');
                    } else {
                        $("#user_id").val(null);
                        $("input[name=name]").prop("disabled", true);
                        $("input[name=phone]").prop("disabled", true);
                        $("input[name=email]").prop("disabled", true);
                        $("input[name=name]").val(null);
                        $("input[name=phone]").val(null);
                        $("input[name=email]").val(null);
                    }
                })

                //add money mask and
                $("input[name=amount]").mask('000.000.000.000.000,00', {
                    reverse: true
                });

                //add prefix R$
                $("input[name=amount]").on('keyup', function () {
                    var value = $(this).val();
                    value = value.replace(/\D/g, "");
                    value = (value / 100).toFixed(2) + '';
                    value = value.replace(".", ",");
                    value = value.replace(/(\d)(?=(\d{3})+\.)/g, "$1.");
                    value = "R$ " + value;
                    $(this).val(value);
                });

                $('#qrcode-copy-button').on('click', function (e) {
                    var $qrcodeValue = $("#qrcode-value").val();
                    navigator.clipboard.writeText($qrcodeValue);
                })

            })
        </script>
    @endif
@endsection
