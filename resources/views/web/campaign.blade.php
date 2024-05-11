@extends('web.master.master')
@section('content')
    @if(!empty($campaign))
        <style>
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

            .overlay {
                position: fixed;
                width: 100%;
                height: 100%;
                top: 0;
                left: 0;
                background-color: rgba(255, 255, 255, 0.8); /* cor de fundo com transparência */
                z-index: 9999; /* z-index alto para sobrepor todos os elementos */
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .spinner-border {
                width: 3rem;
                height: 3rem;
                color: #007bff; /* cor do spinner, pode ser alterada */
            }

            .cardData input{
                color: #676767;
                font-size: 15px;
                font-weight: 300;
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                height: 36px;
                border: 1px solid #d9d9d9;
                border-radius: 4px;
                box-shadow: none;
                background-color: #FDFDFD;
                box-sizing: border-box;
                padding: 0;
                -webkit-transition: border-color .15s linear, box-shadow .15s linear;
                -moz-transition: border-color .15s linear, box-shadow .15s linear;
                -ms-transition: border-color .15s linear, box-shadow .15s linear;
                -o-transition: border-color .15s linear, box-shadow .15s linear;
                transition: border-color .15s linear, box-shadow .15s linear;
            }
        </style>
        <link rel="stylesheet" href="../assets/frontend/css/card-js.min.css">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <div class="overlay d-none" id="loadingOverlay">
            <div class="spinner-border" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>

        <div id="fh5co-wrapper">
            <div class="fh5co-hero fh5co-hero-2">
                <div class="fh5co-overlay"></div>
                <div class="fh5co-cover fh5co-cover_2 text-center" data-stellar-background-ratio="0.5"
                     style="background-image: url({{url($campaign->cover())}}); background-position-y: center;">
                    <div class="desc animate-box">
                        <h2>{{$campaign->title}}</h2>
                        <span>{{$campaign->model}}</span>
                        <span><a class="btn btn-primary btn-lg" href="#apoiar">{{$campaign->status == 1 ? 'Apoiar essa campanha' : 'Ver Campanha'}}</a></span>
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
                            <h2><strong>Responsável - {{$campaign->user->name}}</strong></h2>
                            <h2><strong>CPF - {{explode('.',trim($campaign->user->cpf))[0]}}.***.***-**</strong></h2>
                            <div class="row text-center justify-content-center">
                                <span><a class="btn btn-primary btn-lg" href="#apoiar">{{$campaign->status == 1 ? 'Apoiar essa campanha' : 'Ver Campanha'}}</a></span>
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
                            <h4 class="fh5co-number">Total Arrecadado</h4>
                            <div class="mt-3 mb-5">
                                <div class="col text-center">
                                    <p class="text-center h2" style="margin-top: 10px;margin-bottom: 10px">
                                        R$ {{number_format($campaign->donations->where('status', 3)->sum('amount'), 2, ',', '.')}}
                                    </p>
                                </div>
                            </div>
                            @if($campaign->status == 1)
                                <h4 class="fh5co-number">Apoiar!</h4>
                                <div class="mt-3">
                                    <div class="col text-center">
                                        <p class="text-center h2" style="margin-top: 10px;margin-bottom: 10px">
                                            Apoiar essa campanha
                                        </p>
                                        <span class="text-center h3">Doe o quanto você puder, mas não deixe de doar!!</span>
                                    </div>
                                    <div class="row d-flex justify-content-center mt-3">
                                        <label class="col-12 p-5 mt-3 h3 text-center">Valor do apoio</label>
                                        <input class="col-4 p-4 mt-3 text-center h3" id="amount" name="amount" required value="0,00">
                                    </div>
                                    <div class="row d-flex justify-content-center mt-3">
                                        <div class="row col-lg-4 d-flex justify-content-center my-3">
                                            @if(Auth::check())
                                                <div class="row justify-content-center">
                                                    <div class="col-12">
                                                        <form>
                                                            <input type="hidden" name="campaign_id" value="{{$campaign->id}}">
                                                            <input type="hidden" name="user_id" id="user_id">
                                                            <input type="hidden" name="amount">
                                                            <button class="btn btn-primary p-5 mt-3 btnDoarPix col-12" type="button">Apoiar com PIX</button>
                                                        </form>
                                                        <div class="formPix d-none mt-3">
                                                            <div class="row">
                                                                <!-- show qrcode -->
                                                                <div class="col-md-12">
                                                                    <div class="qrcode-container">
                                                                        <div class="col-12 text-center">
                                                                            <img src="data:image/png;base64," id="qrcode" style="width: inherit;max-width: 300px"/>
                                                                            <input type="hidden" id="qrcode-value">
                                                                            <button class="btn btn-primary col-12" id="qrcode-copy-button">Copiar Código</button>
                                                                            <h4>Após o pagamento, os dados ficarão disponíveis na sua conta, no menu "Meus Apoios"</h4>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 mt-3 mt-lg-0">
                                                        <button class="btn btn-primary p-5 mt-3 btnDoarCartao col-12" type="button">Apoiar com Cartão</button>
                                                        <div class="formCard d-none mt-3">
                                                            <h4 class="my-3">Dados do Cartão</h4>
                                                            <div class="card-js">
                                                                <input required class="card-number my-custom-class" name="card-number" placeholder="Número do Cartão">
                                                                <input required class="name" id="the-card-name-id" name="card-holders-name" placeholder="Nome (igual ao Cartão)">
                                                                <input required class="expiry-month" name="expiry-month">
                                                                <input required class="expiry-year" name="expiry-year">
                                                                <input required class="cvc" name="cvc">
                                                            </div>
                                                            <div class="cardData mt-3">
                                                                <h4 class="my-3">Dados do Proprietário do Cartão</h4>
                                                                <input required class="name my-1 px-3 col-12" id="name" name="cardName" placeholder="Nome">
                                                                <input required class="name my-1 px-3 col-12" id="email" name="cardEmail" placeholder="Email">
                                                                <input required class="cpf my-1 px-3 col-12" id="cpf" name="cardCpf" placeholder="CPF">
                                                                <input required class="name my-1 px-3 col-12" id="phone" name="cardPhone" placeholder="Celular">
                                                                <input required class="name my-1 px-3 col-12" id="address" name="cardAddressComplement" placeholder="Endereço">
                                                                <input required class="name my-1 px-3 col-12" id="number" name="cardAddressNumber" placeholder="Número">
                                                                <input required class="name my-1 px-3 col-12" id="postal-code" name="cardPostalCode" placeholder="CEP">
                                                            </div>
                                                            <h4 class="my-4">Após o pagamento, os dados ficarão disponíveis na sua conta, no menu "Meus Apoios"</h4>
                                                            <button id="payWithCard" class="btn btn-primary p-2" type="button">Apoiar</button>
                                                            <h4 class="my-3 py-2 d-none" style="color: white;background: #2b542c" id="pagtoAprovado">Pagamento Aprovado</h4>
                                                            <h4 class="my-3 py-2 d-none" style="color: white;background: #a94442" id="pagtoNaoAprovado">Pagamento Não Aprovado</h4>
                                                        </div>
                                                    </div>
                                                </div>

                                            @else
                                                <a class="col-md-8 btn btn-primary p-5 mt-3 btnLogin" href="{{ route('sessao.login', ['campaign' => $campaign->slug]) }}">Entrar para apoiar</a>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-header btn btn-primary" id="heading1">
                            <h1>
                                <button class="card-link" style="background-color: transparent;border: none;color: whitesmoke" aria-expanded="false" aria-controls="description1">
                                    Ver Apoios
                                </button>
                            </h1>
                        </div>
                        <div id="description1" class="collapse" aria-labelledby="heading1">
                            <div class="card-body p-0">
                                <div class="dash_content_app_box_stage">
                                    <table id="dataTable" class="nowrap" style="width: 100%; border-collapse: collapse;">
                                        <thead>
                                        <tr class="text-center">
                                            <th style="border: 1px solid #dee2e6;">Apoiador</th>
                                            <th style="border: 1px solid #dee2e6;">CPF</th>
                                            <th style="border: 1px solid #dee2e6;">Valor</th>
                                            <th style="border: 1px solid #dee2e6;">Data</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($campaign->donations()->where('status', 3)->get() as $index => $donation)
                                            <tr class="text-center" style="background-color: {{ $index % 2 == 0 ? '#f8f9fa' : 'white' }};">
                                                <td style="border: 1px solid #dee2e6; max-width: 200px; word-wrap: break-word; white-space: normal;">
                                                    {{ explode(' ', trim($donation->user->name))[0] }}
                                                </td>
                                                <td style="border: 1px solid #dee2e6;">
                                                    {{ explode('.', trim($donation->user->cpf))[0] }}.***.***-**
                                                </td>
                                                <td style="border: 1px solid #dee2e6;">
                                                    R$ {{ number_format($donation->amount, 2, ',', '.') }}
                                                </td>
                                                <td style="border: 1px solid #dee2e6;">
                                                    {{ date('d/m/Y H:i', strtotime($donation->created_at)) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script src="../assets/frontend/js/card-js.min.js"></script>
        <script>
            document.addEventListener("DOMContentLoaded", function() {
                // Espera até que a página seja totalmente carregada para evitar o erro
                var tokenMeta = document.querySelector('meta[name="csrf-token"]');

                if (tokenMeta) { // Verifica se a tag meta foi encontrada
                    var token = tokenMeta.getAttribute('content'); // Obtém o conteúdo do token
                }

                var $Cpf = $("input[id=cpf]");
                $Cpf.mask('000.000.000-00', {
                    reverse: true
                });
                var $phone = $("input[id=phone]");
                $phone.mask("00 00000-0000", {
                    reverse: true
                });

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                function showLoading() {
                    document.getElementById('loadingOverlay').classList.remove('d-none');
                }

                // Ocultar o spinner de carregamento
                function hideLoading() {
                    document.getElementById('loadingOverlay').classList.add('d-none');
                }

                $('.btnDoarPix').on('click', function (e) {
                    var amount = $("input[name=amount]").val();
                    if(amount == "0,00"){
                        alert('Informe um valor a ser doado')
                        return
                    }
                    showLoading();
                    $('.formCard').addClass('d-none');

                    $.ajax({
                        method: "POST",
                        url: "/getPixQrCode",
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        data: {
                            campaign_id: $("input[name=campaign_id]").val(),
                            amount: amount
                        },
                        success: function (data) {
                            console.log(data);
                            hideLoading();
                            $('#qrcode').attr('src', 'data:image/png;base64,' + data['qrCode']['encodedImage']);
                            $('#qrcode-value').val(data['qrCode']['qrCode']);
                        },
                        error: function (data) {
                            console.log(data);
                            hideLoading();
                        },
                        dataType: "json"
                    })
                    $('.formPix').removeClass('d-none');
                });

                $('#payWithCard').on('click', function (e) {
                    var amount = $("input[name=amount]").val();
                    if(amount == "0,00"){
                        alert('Informe um valor a ser doado')
                        return
                    }
                    showLoading();
                    $('.formPix').addClass('d-none');
                    $.ajax({
                        method: "POST",
                        url: "/payWithCard",
                        headers: {
                            'X-CSRF-TOKEN': token
                        },
                        data: {
                            campaign_id: $("input[name=campaign_id]").val(),
                            amount: $("input[name=amount]").val(),
                            cardNumber: $("input[name=card-number]").val(),
                            cardHoldersName: $("input[name=card-holders-name]").val(),
                            cardMonth: $("input[name=expiry-month]").val(),
                            cardYear: $("input[name=expiry-year]").val(),
                            cardCvv: $("input[name=cvc]").val(),
                            cardName: $("input[name=cardName]").val(),
                            cardEmail: $("input[name=cardEmail]").val(),
                            cardCpf: $("input[name=cardCpf]").val(),
                            cardPhone: $("input[name=cardPhone]").val(),
                            cardAddressComplement: $("input[name=cardAddressComplement]").val(),
                            cardAddressNumber: $("input[name=cardAddressNumber]").val(),
                            cardPostalCode: $("input[name=cardPostalCode]").val(),
                        },
                        success: function (data) {
                            console.log(data);
                            hideLoading();
                            if(data['status'] === 'CONFIRMED') {
                                $('#pagtoAprovado').removeClass('d-none');
                            } else {
                                var errors = data['errors'];
                                errors.forEach(error => {
                                    Toastify({
                                        text: error['description'],
                                        duration: 3000,
                                        gravity: "top",
                                        position: "right",
                                        backgroundColor: "#e74c3c",
                                        stopOnFocus: true,
                                        onClick: function () {}
                                    }).showToast();
                                })
                                $('#pagtoNaoAprovado').removeClass('d-none');
                            }
                        },
                        error: function (data) {
                            console.log(data);
                            hideLoading();
                        },
                    })
                })

                const cardHeader = document.querySelector('.card-header');
                const description = document.querySelector('.collapse');

                cardHeader.addEventListener('click', function() {
                    const expanded = description.classList.contains('show') ? 'false' : 'true';
                    description.classList.toggle('show');
                    this.querySelector('.card-link').setAttribute('aria-expanded', expanded);
                });

                $('.btnDoarCartao').on('click', function (e) {
                    $('.formCard').removeClass('d-none');
                    $('.formPix').addClass('d-none');
                });

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

            });
        </script>
    @endif
@endsection
