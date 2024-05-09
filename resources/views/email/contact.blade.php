@component('mail::message')
  Olá, recebemos uma nova mensagem através do site :)

  Nome: <b>{{$reply_name}}</b>

  Email: <b>{{$reply_email}}</b>

  Mensagem:
  @component('mail::panel')
    {{$message}}
  @endcomponent
@endcomponent
