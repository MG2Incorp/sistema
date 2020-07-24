@component('mail::message')

@component('mail::panel')

Essa é uma mensagem gerada automaticamente pelo sistema MG2 Incorp. Você foi cadastrado no sistema com os seguintes dados:
<br>
<div style="text-align: center">E-Mail: {{ $email }}</div>
<br>
<div style="text-align: center">Senha: {{ $pass }}</div>
<br>

@endcomponent

@endcomponent
