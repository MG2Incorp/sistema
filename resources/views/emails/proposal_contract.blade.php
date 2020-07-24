@component('mail::message')

@component('mail::panel')

Olá {{ $proponent ? $proponent->name : '' }}, estou enviando em anexo uma cópia de seu contrato para apreciação e assinatura.

Em caso de duvidas favor entrar em contato com seu corretor.

Esta é uma mensagem enviada através do sistema MG2 – Incorp.

@endcomponent

@endcomponent
