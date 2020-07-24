@component('mail::message')

@component('mail::panel')

<b>Nome Completo:</b> {{ $request['name'] }}

<b>Empresa:</b> {{ $request['company'] }}

<b>E-Mail:</b> {{ $request['email'] }}

<b>Telefone / Celular:</b> {{ $request['phone'] }}

<b>Cargo na Empresa:</b> {{ $request['role'] }}

<b>Cidade / UF:</b> {{ $request['local'] }}

<b>Conte sua necessidade:</b> {{ $request['message'] }}

@endcomponent

@endcomponent
