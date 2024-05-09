@extends('admin.master.master')
@section('content')
<section class="dash_content_app">

    <header class="dash_content_app_header">
        <h2 class="icon-search">Filtro</h2>
        <div class="dash_content_app_header_actions">
            <nav class="dash_content_app_breadcrumb">
                <ul>
                    <li><a href="">Dashboard</a></li>
                    <li class="separator icon-angle-right icon-notext"></li>
                    <li><a href="">Campanhas</a></li>
                    <li class="separator icon-angle-right icon-notext"></li>
                    <li><a href="" class="text-orange">Filtro</a></li>
                </ul>
            </nav>

            <a href="{{route('admin.campaigns.create')}}" class="btn btn-orange icon-plus ml-1">Criar Campanha</a>
        </div>
    </header>

    <div class="dash_content_app_box">
        @if(session()->exists('message'))
            <div class="message message-green">
                <p class="icon-asterisk">{{ session()->get('message') }}</p>
            </div>
        @endif
        <div class="dash_content_app_box_stage">
            <div class="realty_list">
                @foreach($campaigns as $campaign)
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
                                    <span class="realty_list_item_description_title">Cotas:</span>
                                    <span class="realty_list_item_description_content">{{$campaign->q_donations}}</span>
                                </div>
                            </div>
                            <div class="realty_list_item_card">
                                <div class="realty_list_item_card_image">
                                    <span class="icon-money"></span>
                                </div>
                                <div class="realty_list_item_card_content">
                                    <span class="realty_list_item_description_title">Meta:</span>
                                    <span class="realty_list_item_description_content">{{$campaign->goal}}</span></span>
                                </div>
                            </div>
                            <div class="realty_list_item_card">
                                <div class="realty_list_item_card_image">
                                    <span class="icon-bar-chart"></span>
                                </div>
                                <div class="realty_list_item_card_content">
                                    <span class="realty_list_item_description_title">Cotas Pagas:</span>
                                    <span class="realty_list_item_description_content">{{$campaign->donations()->where('status', '3')->count()}}</span></span>
                                </div>
                            </div>
                            <div class="realty_list_item_card">
                                <div class="realty_list_item_card_image">
                                    <span class="icon-bar-chart"></span>
                                </div>
                                <div class="realty_list_item_card_content">
                                    <span class="realty_list_item_description_title">Cotas Disponíveis:</span>
                                <span class="realty_list_item_description_content">{{$campaign->donations()->where('status', '1')->count()}}</span></span>
                                </div>
                            </div>
                            <div class="realty_list_item_card">
                                <div class="realty_list_item_card_image">
                                    <span class="icon-bar-chart"></span>
                                </div>
                                <div class="realty_list_item_card_content">
                                    <span class="realty_list_item_description_title">Cotas Reservadas:</span>
                                    <span class="realty_list_item_description_content">{{$campaign->donations()->where('status', '2')->count()}}</span></span>
                                </div>
                            </div>
                        </div>
                        <div class="realty_list_item_actions mt-1">
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
@endsection
