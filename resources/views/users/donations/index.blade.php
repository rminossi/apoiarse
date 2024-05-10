@extends('users.master.master')
@section('content')
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
        <div class="dash_content_app_box_stage">
            <table id="dataTable" class="nowrap stripe" style="width: 100%">
                <thead>
                <tr>
                    <th>Campanha</th>
                    <th>Valor</th>
                    <th>Data</th>
                    <th>Situação</th>
                </tr>
                </thead>
                <tbody>
                    @foreach($donations as $donation)
                        <tr>
                            <td style="max-width: 200px;word-wrap: break-word; white-space: normal;"><a href="/campanhas/{{$donation->campaign->slug}}" class="text-orange">{{$donation->campaign->title}}</a></td>
                            <td>R$ {{number_format($donation->amount, 2, ',', '.')}}</td>
                            <td>{{date('d/m/Y H:i', strtotime($donation->created_at))}}</td>
                            <td>
                                @if($donation->status == 1)
                                    Pendente
                                @elseif($donation->status == 2)
                                    Expirado
                                @else
                                    Aprovado
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</section>
@endsection
