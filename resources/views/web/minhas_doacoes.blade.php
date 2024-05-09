@extends('web.master.master')
@section('content')
    <div class="fh5co-hero">
        <div class="fh5co-overlay"></div>
        <div class="fh5co-cover text-center" data-stellar-background-ratio="0.5"
             style="background-image: url({{ asset('assets/frontend/images/apoiarse.png') }});">
            <div class="desc animate-box">
                <h2>Minhas Doações</h2>
                <span>Consulte suas doações</span>
            </div>
        </div>
    </div>
    <div id="fh5co-blog-section" class="fh5co-section-gray">
        <div class="container">
            <div class="row">
                <div class="col-md-8 mb-3 col-md-offset-2 text-center heading-section animate-box">
                    <h3>Consultar doações por CPF</h3>
                    <p>Insira o CPF em que foram feitas as doações</p>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row">
                <form method="" action="/meus-numeros" class="col-md-4 col-md-offset-4 text-center animate-box">
                    <div class="form-group">
                        <input type="text" class="form-control" id="cpf" name="cpf" placeholder="CPF">
                    </div>
                    <button class="btn btn-primary btn-lg">Consultar</button>
                </form>
            </div>
        </div>
        @if(isset($campaigns))
            <div class="container">
                <div class="row">
                    <div class="col-md-8 mb-3 col-md-offset-2 text-center heading-section animate-box">
                        <h3>Minhas Doações</h3>
                    </div>
                </div>
                @foreach($campaigns as $campaign)
                    <div class="text-center">
                        <a href="/campaigns/{{ $campaign->slug }}"><h3 class="text-center">{{$campaign->title}}</h3></a>
                    </div>
                        <div class="row d-flex my-5">
                            @foreach($user->donations->where('campaign_id', $campaign->id)->where('status', 3) as $donation)
                            <div class="border border-primary col-md-1 py-3 text-center center m-1">
                                {{ $donation->amount }}
                            </div>
                            @endforeach
                        </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection
