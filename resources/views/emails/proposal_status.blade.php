@component('mail::message')

@component('mail::panel')

Prezado {{ $user->name }},

{{ $content }}

Esta é uma mensagem enviada através do sistema MG2 – Incorp.

@endcomponent

@endcomponent
