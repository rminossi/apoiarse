<header id="fh5co-header-section">
    <div class="col-12 px-5" style="position: fixed;background: rgba(228,228,228,0.84)">
        <div class="nav-header">
            <a href="#" class="js-fh5co-nav-toggle fh5co-nav-toggle"><i></i></a>
            <a href="{{route('web.home')}}"><img class="my-1" src="{{url(asset('assets/frontend/images/apoiarse_logo.png'))}}"
                                                 width="150" alt="logomarca Emicê Garage" id="fh5co-logo"></img></a>
            <nav id="fh5co-menu-wrap" role="navigation" class="mt-3">
                <ul class="sf-menu" id="fh5co-primary-menu">
                    <li class="{{request()->routeIs('web.home') ? 'active' : ''}}">
                        <a href="{{route('web.home')}}">Início</a>
                    </li>
                    <li class="{{request()->routeIs('web.campaigns') ? 'active' : ''}}">
                        <a href="{{route('web.campaigns')}}">Campanhas</a>
                    </li>
                    <li class="{{request()->routeIs('web.sobre') ? 'active' : ''}}">
                        <a href="{{route('web.sobre')}}">Sobre</a>
                    </li>
                    <li>
                    <li class="{{request()->routeIs('web.contato') ? 'active' : ''}}">
                        <a href="{{route('web.contato')}}">Contato</a>
                    </li>
                    <li>
                        <a href="{{route('usuario.home')}}">Criar Campanha</a>
                    <li>
                        <a href="usuario/campanhas/create">{{Auth::check() ? "Painel" : 'Entrar'}}</a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</header>

