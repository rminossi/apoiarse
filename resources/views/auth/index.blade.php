<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="../assets/backend/css/reset.css" />
    <link rel="stylesheet" href="../assets/backend/css/boot.css" />
    <link rel="stylesheet" href="../assets/backend/css/login.css" />

    <link rel="icon" type="image/png" href="../assets/frontend/images/favicon.png" />
    <meta name="csrf-token" content="{{@csrf_token()}}">

    <title>Apoiar-se Online - Entrar</title>
</head>

<body>

    <div class="ajax_response"></div>

    <div class="dash_login">
        <div class="dash_login_left" style="flex-basis: 100%">
            <article class="dash_login_left_box">
                <header class="dash_login_box_headline text-center">
                    <img src="{{url(asset('assets/frontend/images/apoiarse_logo.png'))}}" width="150">
                    <h2>Entrar</h2>
                </header>
                <form name="login" action="{{route('sessao.enviar-login')}}" method="post" autocomplete="on">
                    <label>
                        <span class="field icon-envelope">E-mail:</span>
                        <input type="email" name="email" placeholder="Informe seu e-mail" required />
                    </label>
                    <label>
                        <span class="field icon-unlock-alt">Senha:</span>
                        <input type="password" name="password_check" placeholder="Informe sua senha" />
                    </label>
                    <button class="gradient gradient-green radius icon-sign-in">Entrar</button>
{{--                    <a href="{{route('recuperar-senha')}}" class="forgot_pass">Esqueceu sua senha?</a>--}}
                    <div class="text-center mt-2">
                        <a href="{{route('sessao.register')}}" class="forgot_pass">Criar uma conta</a>
                    </div>
                </form>
                <footer>
                    <p>Desenvolvido por <a href="https://twitter.com/rminossi">Rafael Minossi</a></p>
                    <p>&copy; <?= date("Y"); ?> - Todos os Direitos Reservados</p>
                </footer>
            </article>
        </div>
    </div>

<script src="../assets/backend/js/jquery.js"></script>
<script src="../assets/backend/js/login.js"></script>

</body>

</html>
