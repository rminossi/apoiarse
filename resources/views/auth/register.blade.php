<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">

    <link rel="stylesheet" href="../assets/backend/css/reset.css" />
    <link rel="stylesheet" href="../assets/backend/css/boot.css" />
    <link rel="stylesheet" href="../assets/backend/css/login.css" />

    <link rel="icon" type="image/png" href="assets/frontend/images/favicon.png" />
    <meta name="csrf-token" content="{{@csrf_token()}}">

    <title>Apoiar-se Online - Registro</title>
</head>

<body>

    <div class="ajax_response"></div>

    <div class="dash_login">
        <div class="dash_login_left" style="flex-basis: 100%">
            <article class="dash_login_left_box">
                <header class="dash_login_box_headline text-center">
                    <img src="{{url(asset('assets/frontend/images/apoiarse_logo.png'))}}" width="150">
                    <h2>Registro de Usuário</h2>
                </header>
                <form name="register" action="{{route('sessao.enviar-registro')}}" method="post" autocomplete="off">
                    @csrf
                    <label>
                        <span class="field icon-person">Nome Completo:</span>
                        <input type="text" name="name" placeholder="Informe seu nome" required />
                    </label>
                    <label>
                        <span class="field icon-envelope">E-mail:</span>
                        <input type="email" name="email" placeholder="Informe seu e-mail" required />
                    </label>
                    <label>
                        <span class="field icon-phone">Telefone:</span>
                        <input type="text" name="phone" placeholder="Informe seu telefone" required />
                    </label>
                    <label>
                        <span class="field icon-user-plus">CPF:</span>
                        <input type="text" name="cpf" placeholder="Informe seu CPF" required />
                    </label>
                    <label>
                        <span class="field icon-unlock-alt">Senha:</span>
                        <input type="password" name="password" required placeholder="Crie sua senha" />
                    </label>
                    <label>
                        <span class="field icon-unlock-alt">Confirme sua senha:</span>
                        <input type="password" name="password_confirmation" required
                               placeholder="Confirme sua senha" />
                    </label>
                    <button class="gradient gradient-green radius icon-sign-in">Registrar</button>
                    <div class="text-center mt-2">
                        <a href="{{route('sessao.login')}}" class="forgot_pass">Já possuo conta</a>
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
