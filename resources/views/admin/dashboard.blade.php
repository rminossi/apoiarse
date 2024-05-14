@extends('admin.master.master')

@section('content')
    <div style="flex-basis: 100%;">
        <section class="dash_content_app">
            <header class="dash_content_app_header">
                <h2 class="icon-tachometer">Painel</h2>
            </header>
            <div class="dash_content_app_box">
                <section class="app_dash_home_stats">
                    <article class="control radius">
                        <h4 class="icon-users">Usuários Cadastrados</h4>
                        <h1 class="mt-3 text-center"><b>{{$users->count()}}</b></h1>
                    </article>
                    <article class="blog radius">
                        <h4 class="icon-table">Campanhas</h4>
                        <p class="mt-2"><b>Ativas:</b> {{$campaigns->where('status', '1')->count()}}</p>
                        <p><b>Inativas:</b> {{$campaigns->where('status', '2')->count()}}</p>
                        <p><b>Encerradas:</b> {{$campaigns->where('status', '3')->count()}}</p>
                    </article>
                    <article class="users radius">
                        <h4 class="icon-bar-chart">Doações</h4>
                        <p><b>Total:</b> {{$donations->count()}}</p>
                        <p><b>Pagas:</b> {{$donations->where('status', '1')->count()}}</p>
                        <p><b>Pendentes:</b> {{$donations->where('status', '3')->count()}}</p>
                    </article>
                </section>
            </div>
        </section>
        <section class="dash_content_app" style="margin-top: 40px;">
            <header class="dash_content_app_header">
                <h2 class="icon-hourglass">Últimas Campanhas Cadastradas</h2>
            </header>
            <div class="dash_content_app_box">
                <div class="dash_content_app_box_stage">
                    <div class="realty_list">
                        @foreach($latest_campaigns as $campaign)
                            <div class="realty_list_item mt-1 mb-1">
                                <div class="realty_list_item_actions_stats">
                                    <img src="{{url($campaign->cover())}}" alt="">
                                </div>
                                <div class="realty_list_item_content">
                                    <h4>{{$campaign->title}}</h4>
                                    <div class="realty_list_item_card">
                                        <div class="realty_list_item_card_image">
                                            <span class="icon-table"></span>
                                        </div>
                                        <div class="realty_list_item_card_content">
                                            <span class="realty_list_item_description_title">Apoios:</span>
                                            <span class="realty_list_item_description_content">{{$campaign->q_donations}}</span>
                                        </div>
                                    </div>
                                    <div class="realty_list_item_card">
                                        <div class="realty_list_item_card_image">
                                            <span class="icon-money"></span>
                                        </div>
                                        <div class="realty_list_item_card_content">
                                            <span class="realty_list_item_description_title">Meta:</span>
                                            <span class="realty_list_item_description_content">{{$campaign->goal ?? 'Sem Meta'}}</span></span>
                                        </div>
                                    </div>
                                    <div class="realty_list_item_card">
                                        <div class="realty_list_item_card_image">
                                            <span class="icon-bar-chart"></span>
                                        </div>
                                        <div class="realty_list_item_card_content">
                                            <span class="realty_list_item_description_title">Apoios Pagos:</span>
                                            <span class="realty_list_item_description_content">{{$campaign->donations()->where('status', '3')->count()}}</span></span>
                                        </div>
                                    </div>
                                    <div class="realty_list_item_card">
                                        <div class="realty_list_item_card_image">
                                            <span class="icon-bar-chart"></span>
                                        </div>
                                        <div class="realty_list_item_card_content">
                                            <span class="realty_list_item_description_title">Apoios Pendentes:</span>
                                            <span class="realty_list_item_description_content">{{$campaign->donations()->where('status', '1')->count()}}</span></span>
                                        </div>
                                    </div>
                                    <div class="realty_list_item_card">
                                        <div class="realty_list_item_card_image">
                                            <span class="icon-bar-chart"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="realty_list_item_actions">
                                    <div class="ml-auto d-flex">
                                        <a href="{{route('admin.campaigns.edit', ['campaign' => $campaign->id])}}" class="btn btn-green icon-pencil-square-o">Editar Campanha</a>
                                        <form method="POST" action="{{ route('admin.campaigns.update', ['campaign' => $campaign->id]) }}">
                                            {{ csrf_field() }}
                                            {{ method_field('delete') }}
                                            <button class="btn btn-red icon-trash-o" type="submit">Excluir Campanha</button>
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
