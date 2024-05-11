@extends('users.master.master')
@section('content')
    <style>
        .modal {
            display: none; /* Inicia escondido */
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
        }

        /* Estilo para o conteúdo do modal */
        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }

        /* Estilo para o botão de fechar */
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
<section class="dash_content_app">

    <header class="dash_content_app_header">
        <h2 class="icon-search">Filtro</h2>

        <div class="dash_content_app_header_actions">
            <nav class="dash_content_app_breadcrumb">
                <ul>
                    <li><a href="">Dashboard</a></li>
                    <li class="separator icon-angle-right icon-notext"></li>
                    <li><a href="" class="text-orange">Meus Apoios</a></li>
                </ul>
            </nav>
        </div>
    </header>

    @include('users.donations.filter')

    <div class="dash_content_app_box">
        <div id="myModal" class="modal">
            <div class="modal-content text-center">
                <span class="close">&times;</span>
                <h2>Qr Code</h2>
                <img src="data:image/png;base64," id="qrcode" style="width: inherit;max-width: 300px"/>
                <h4>Após o pagamento, fechar a janela e atualizar a página.</h4>
            </div>
        </div>
        <div class="dash_content_app_box_stage">
            <table id="dataTable" class="nowrap stripe" style="width: 100%">
                <thead>
                <tr>
                    <th>Campanha</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Situação</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                        <tr>
                            <td style="max-width: 200px;word-wrap: break-word; white-space: normal;"><a href="/campanhas/{{$donation->campaign->slug}}" class="text-orange">{{$donation->campaign->title}}</a></td>
                            <td>R$ {{number_format($donation->amount, 2, ',', '.')}}</td>
                            <td>{{date('d/m/Y H:i', strtotime($donation->created_at))}}</td>
                            <td class="{{$donation->status == 3 ? 'bg-green' : ($donation->status == 2 ? 'bg-red' : 'bg-yellow')}}">{{$donation->status == 3 ? 'Recebido' : ($donation->status == 2 ? 'Cancelado' : 'Pendente') }}</td>
                            <td class="text-center"><button class="btn btn-green icon-clipboard" {{$donation->status == 1 ? '' : 'disabled'}} onclick="copyToClipboard('{{$donation->pix_key}}')">Copiar Chave Pix</button></td>
                            <td class="text-center"><button class="btn btn-green icon-eye" {{$donation->status == 1 ? '' : 'disabled'}} onclick="showQrCode('{{$donation->pix_qrcode}}')">Exibir QR Code</button></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function copyToClipboard(code) {
            //copy code to clipboard, onde code é uma string contendo o valor do pix
            navigator.clipboard.writeText(code);

        }

        var modal = document.getElementById("myModal");

        // Obtém o elemento <span> que fecha o modal
        var span = document.getElementsByClassName("close")[0];

        // Quando o usuário clicar no botão, abra o modal
        function showQrCode(code) {
            modal.style.display = "block";
            document.getElementById("qrcode").src = 'data:image/png;base64,' + code;
        }

        // Quando o usuário clicar em <span> (x), feche o modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // Quando o usuário clicar fora do modal, feche-o
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</section>
@endsection
