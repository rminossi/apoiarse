@extends('users.master.master')

@section('content')
    <div style="flex-basis: 100%;">
        <section class="dash_content_app">
            <header class="dash_content_app_header">
                <h2 class="icon-tachometer">Painel</h2>
            </header>
            <div class="dash_content_app_box">
                <section class="app_dash_home_stats" style="justify-content: space-around">
                    <article class="control radius">
                        <h4 class="icon-users">Apoios Realizados</h4>
                        <h1 class="mt-3 text-center"><b>{{$donations->count()}}</b></h1>
                    </article>
                    <article class="control radius">
                        <h4 class="icon-table">Campanhas</h4>
                        <p class="mt-2"><b>Ativas:</b> {{$campaigns->where('status', '1')->count()}}</p>
                        <p><b>Encerradas:</b> {{$campaigns->where('status', '3')->count()}}</p>
                    </article>
                </section>
            </div>
        </section>
        <section class="dash_content_app" style="margin-top: 40px;">
            <header class="dash_content_app_header">
                <h2 class="icon-hourglass">Últimos Apoios Realizados</h2>
            </header>
            <div class="dash_content_app_box">
                <div class="dash_content_app_box_stage">
                    <table class="nowrap stripe text-center" width="100" style="width: 100% !important; border-collapse: separate; border-spacing: 1em;">
                        <thead>
                        <tr>
                            <th>Campanha</th>
                            <th>Valor</th>
                            <th>Data</th>
                            <th>Situação</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($last_donations as $donation)
                            <tr>
                                <td><a href="/campanhas/{{$donation->campaign->slug}}" class="text-orange" target="_blank">{{$donation->campaign->title}}</a></td>
                                <td>R$ {{number_format($donation->amount, 2, ',', '.')}}</td>
                                <td>{{date('d/m/Y H:i', strtotime($donation->created_at))}}</td>
                                <td>
                                    @if($donation->status == 1)
                                        Aprovado
                                    @elseif($donation->status == 2)
                                        Reprovado
                                    @else
                                        Pendente
                                    @endif
                                </td>
                            </tr>
                            <hr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>
        <section class="dash_content_app" style="margin-top: 40px;">
            <header class="dash_content_app_header">
                <h2 class="icon-hourglass">Últimas Campanhas Cadastradas</h2>
            </header>
            <div class="dash_content_app_box">
                <div class="dash_content_app_box_stage">
                    <div class="realty_list">
                        @foreach($last_campaigns as $campaign)
                            <div class="realty_list_item mt-1 mb-1">
                                <div class="realty_list_item_content">
                                    <h4 style="width: max-content"><a href="/campanhas/{{$campaign->slug}}" class="text-orange" target="_blank">{{$campaign->title}}</a></h4>
                                    <div class="realty_list_item_card">
                                        <div class="realty_list_item_card_image">
                                            <span class="icon-table"></span>
                                        </div>
                                        <div class="realty_list_item_card_content">
                                            <span class="realty_list_item_description_title">Meta:</span>
                                            <span
                                                class="realty_list_item_description_content">{{floatval($campaign->goal) == 0 ? 'Indefinido' : 'R$ '.number_format($campaign->goal, 2, ',', '.')}}</span>
                                        </div>
                                    </div>
                                    <div class="realty_list_item_card">
                                        <div class="realty_list_item_card_image">
                                            <span class="icon-bar-chart"></span>
                                        </div>
                                        <div class="realty_list_item_card_content">
                                            <span class="realty_list_item_description_title">Total arrecadado:</span>
                                            <span
                                                class="realty_list_item_description_content">R$ {{number_format($campaign->donations()->sum('amount'), 2, ',', '.')}}</span></span>
                                        </div>
                                    </div>
                                    @if(floatval($campaign->goal) != 0)
                                        <div class="realty_list_item_card">
                                            <div class="realty_list_item_card_image">
                                                <span class="icon-bar-chart"></span>
                                            </div>
                                            <div class="realty_list_item_card_content">
                                                <span class="realty_list_item_description_title">Valor restante:</span>
                                                <span
                                                    class="realty_list_item_description_content">{{number_format(floatval($campaign->goal) - $campaign->donations()->sum('amount'), 2, ',', '.')}}</span></span>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                                <div class="realty_list_item_actions">
                                    <div class="ml-auto d-flex">
                                        <a href="{{route('usuario.campanhas.edit', ['campanha' => $campaign->id])}}"
                                           class="btn btn-green icon-pencil-square-o">Editar Campanha</a>
                                        <form method="POST"
                                              action="{{ route('usuario.campanhas.update', ['campanha' => $campaign->id]) }}">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button class="btn btn-red icon-trash-o" type="submit">Excluir Campanha
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection
