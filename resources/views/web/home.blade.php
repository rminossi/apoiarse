@extends('web.master.master')
@section('content')
<div class="fh5co-hero">
	<div class="fh5co-overlay"></div>
	<div class="fh5co-cover text-center" data-stellar-background-ratio="0.5" style="background-image: url({{ asset('assets/frontend/images/apoiarse.png') }});">
		<div class="desc animate-box">
			<h2><strong>Venha apoiar conosco</strong></h2>
			<span><a class="btn btn-primary btn-lg" href="{{route('web.campaigns')}}">Ver nossas campanhas</a></span>
		</div>
	</div>
</div>
@isset($activeCampaigns)
<div id="fh5co-blog-section" class="fh5co-section-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
				<h3>Últimas campanhas</h3>
				<p>Confira aqui as últimas campanhas cadastradas e apoie você também!</p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row row-bottom-padded-md">
			@foreach($activeCampaigns as $campaign)
			<div class="col-lg-4 col-md-4">
				<div class="fh5co-blog animate-box">
					<a href="{{route('web.campaign', ['slug' => $campaign->slug])}}"><img class="img-responsive" src="{{url($campaign->cover())}}" alt=""></a>
					<div class="blog-text">
						<div class="prod-title">
							<h3><a href="{{route('web.campaign', ['slug' => $campaign->slug])}}">{{$campaign->title}}</a></h3>
							<a href="{{route('web.campaign', ['slug' => $campaign->slug])}}" class="btn btn-primary p-3 col-12">{{"Apoiar!"}}</a>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center animate-box">
				<a href="{{route('web.campaigns')}}" class="btn btn-primary btn-lg">Ver Todas</a>
			</div>
		</div>
	</div>
</div>
@endisset
@if(sizeof($finishedCampaigns) > 0)
<div id="fh5co-blog-section" class="fh5co-section-gray">
	<div class="container">
		<div class="row">
			<div class="col-md-8 col-md-offset-2 text-center heading-section animate-box">
				<h3>Sorteios encerrados</h3>
				<p>Veja nossos últimos sorteios realizados, seus detalhes e ganhadores.</p>
			</div>
		</div>
	</div>
	<div class="container">
		<div class="row row-bottom-padded-md">
			@foreach($finishedCampaigns as $campaign)
			<div class="col-lg-4 col-md-4">
				<div class="fh5co-blog animate-box">
					<a href=""><img class="img-responsive" src="{{url($campaign->cover())}}" alt=""></a>
					<div class="blog-text">
						<div class="prod-title">
							<h3><a href="{{route('web.campaign', ['slug' => $campaign->slug])}}">{{$campaign->title}}</a></h3>
							<p>{!!$campaign->description!!}</p>
                            <a href="{{route('web.campaign', ['slug' => $campaign->slug])}}" class="btn btn-primary p-3 col-12">{{"Ver Campanha"}}</a>
						</div>
					</div>
				</div>
			</div>
			@endforeach
		</div>
		<div class="row">
			<div class="col-md-4 col-md-offset-4 text-center animate-box">
				<a href="" class="btn btn-primary btn-lg">Ver Todas</a>
			</div>
		</div>
	</div>
</div>
@endif
@endsection
