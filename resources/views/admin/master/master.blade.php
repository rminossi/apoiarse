<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=0">

    @hasSection('css')
    @yield('css')
    @endif

<link rel="stylesheet" href="/assets/backend/css/reset.css" />
<link rel="stylesheet" href="/assets/backend/css/vendor.css" />
<link rel="stylesheet" href="/assets/backend/css/boot.css" />
<link rel="stylesheet" href="/assets/backend/css/style.css" />

    <link rel="shortcut icon" href="{{url(asset('/assets/frontend/images/favicon.png'))}}">
<script src="/assets/backend/js/jquery.js"></script>
<script src="/assets/backend/js/vendor.js"></script>
<script src="/assets/backend/js/scripts.js"></script>
    <script src="https://cdn.ckeditor.com/4.14.1/standard/ckeditor.js"></script>
    <meta name="csrf-token" content="{{@csrf_token()}}">

    <title>Apoiar-se Online - Painel Administrativo</title>
</head>

<body>

    <div class="ajax_load">
        <div class="ajax_load_box">
            <div class="ajax_load_box_circle"></div>
            <p class="ajax_load_box_title">Aguarde, carregando...</p>
        </div>
    </div>

    <div class="ajax_response"></div>

    <div class="dash">
        <aside class="dash_sidebar">
            <article class="dash_sidebar_user">
                <img class="dash_sidebar_user_thumb bg-white" src="{{url(asset('/assets/backend/images/round.png'))}}" alt="" title="" />

                <h1 class="dash_sidebar_user_name">
                    <a href="">Apoiar-se Online</a>
                </h1>
            </article>

            <ul class="dash_sidebar_nav">
                <li class="dash_sidebar_nav_item {{isActive('admin.home')}}">
                    <a class="icon-tachometer" href="{{route('admin.home')}}">Dashboard</a>
                </li>
                <li class="dash_sidebar_nav_item {{isActive('admin.users')}}"><a class="icon-users" href="{{route('admin.users.index')}}">Usuários</a></li>
                <li class="dash_sidebar_nav_item {{isActive('admin.campaigns.index')}}"><a class="icon-table" href="{{route('admin.campaigns.index')}}">Campanhas</a>
                    <ul class="dash_sidebar_nav_submenu">
                        <li class="{{isActive('admin.campaigns.index')}}"><a href="{{route('admin.campaigns.index')}}">Ver Todas</a></li>
                        <li class="{{isActive('admin.campaigns.create')}}"><a href="{{route('admin.campaigns.create')}}">Criar Nova</a></li>
                    </ul>
                </li>
                <li class="dash_sidebar_nav_item {{isActive('admin.sites')}}"><a class="icon-pencil" href="{{route('admin.site.edit', ['site' => '1'])}}">Configurações do site</a></li>
                <li class="dash_sidebar_nav_item"><a class="icon-reply" href="{{route('web.home')}}">Ver Site</a></li>
                <li class="dash_sidebar_nav_item"><a class="icon-sign-out on_mobile" href="{{route('sessao.logout')}}" target="_blank">Sair</a></li>
            </ul>

        </aside>

        <section class="dash_content">

            <div class="dash_userbar">
                <div class="dash_userbar_box">
                    <div class="dash_userbar_box_content">
                        <span class="icon-align-justify icon-notext mobile_menu transition btn btn-green"></span>
                        <div class="dash_userbar_box_bar no_mobile">
                            <a class="text-red icon-sign-out" href="{{route('sessao.logout')}}">Sair</a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="dash_content_box">
                @yield('content')
            </div>
        </section>
    </div>


    @hasSection('js')
    @yield('js')
    @endif

</body>

</html>
