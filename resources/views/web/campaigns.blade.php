@extends('web.master.master')
@section('content')
<div class="fh5co-hero fh5co-hero-2">
    <div class="fh5co-overlay"></div>
    <div class="fh5co-cover fh5co-cover_2 text-center" data-stellar-background-ratio="0.5" style="background-image: url('{{asset('assets/frontend/images/apoiarse.png')}}');">
        <div class="desc animate-box">
            <h2><strong>Nossas Campanhas</strong></h2>
            <span>Confira abaixo as campanhas atuais e também as finalizadas.</span>
        </div>
    </div>
</div>
<!-- end:header-top -->
<div id="fh5co-portfolio">
    <div class="container">
        @if(sizeof($activeCampaigns) > 0)
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center heading-section animate-box">
                <h3>Campanhas Ativas</h3>
                <p>Confira nossas campanhas ativas, o que significa que ainda dá tempo de apoiar!</p>
            </div>
        </div>
        @isset($activeCampaigns)
        <div class="row row-bottom-padded-md">
            <div class="col-md-12">
                <ul id="fh5co-portfolio-list">
                    @foreach($activeCampaigns as $campaign)
                    <div class="col-lg-4 col-md-4">
                        <div class="fh5co-blog animate-box">
                            <a href="{{route('web.campaign', ['slug' => $campaign->slug])}}"><img class="img-responsive" src="{{url($campaign->cover())}}" alt=""></a>
                            <div class="blog-text">
                                <div class="prod-title">
                                    <h3><a href="" #>{{$campaign->title}}</a></h3>
                                    <a href="{{route('web.campaign', ['slug' => $campaign->slug])}}" class="btn btn-primary">{{"Apoiar!"}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </ul>
            </div>
        </div>
        @endisset
        @endif
        @if(sizeof($finishedCampaigns) > 0)
        <div class="row">
            <div class="col-md-6 col-md-offset-3 text-center heading-section animate-box">
                <h3>Campanhas Encerradas</h3>
                <p>Confira nossas campanhas encerradas.</p>
            </div>
        </div>
        @isset($finishedCampaigns)
        <div class="row row-bottom-padded-md">
            <div class="col-md-12">
                <ul id="fh5co-portfolio-list">
                    @foreach($finishedCampaigns as $campaign)
                    <div class="col-lg-4 col-md-4">
                        <div class="fh5co-blog animate-box">
                            <a href="{{route('web.campaign', ['slug' => $campaign->slug])}}"><img class="img-responsive" src="{{url($campaign->cover())}}" alt=""></a>
                            <div class="blog-text">
                                <div class="prod-title">
                                    <h3><a href="" #>{{$campaign->title}}</a></h3>
                                    <p>{{$campaign->description}}</p>
                                    <a href="" class="btn btn-primary">{{"Comprar - R$".$campaign->price}}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </ul>
            </div>
        </div>
        @endisset
        @endif
    </div>
</div>
<script>
    // Get the modal
    var modal = document.getElementById("myModal");

    // Get the button that opens the modal
    var btn = document.getElementById("myBtn");

    // Get the <span> element that closes the modal
    var span = document.getElementsByClassName("close")[0];

    // When the user clicks on the button, open the modal
    btn.onclick = function() {
        modal.style.display = "block";
    }

    // When the user clicks on <span> (x), close the modal
    span.onclick = function() {
        modal.style.display = "none";
    }

    // When the user clicks anywhere outside of the modal, close it
    window.onclick = function(event) {
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>
@endsection
