@component('mail::message')
  Olá, {{ $name }}.

  Recebemos uma solicitação para redefinir sua senha.

  @component('mail::button', ['url' => $url])
    Clique aqui para redefinir sua senha
  @endcomponent

    Caso não tenha feito esta solicitação, ignore este e-mail.

    Atenciosamente, equipe Apoiar-se.
@endcomponent
