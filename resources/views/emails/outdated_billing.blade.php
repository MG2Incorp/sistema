@component('mail::message')

@component('mail::panel')

Prezado {{ $proponent ? $proponent->name : '' }}, até o momento não identificamos o pagamento referente a:

CONTRATO: {{ $proposal->property->block->building->project->name }}
BLOCO: {{ $proposal->property->block->building->name }}
QUADRA/ANDAR: {{ $proposal->property->block->label }}
NÚMERO: {{ $proposal->property->number }}

Caso já tenha efetuado o pagamento, desconsidere essa mensagem.

Em caso de duvidas favor entrar em contato com seu corretor.

Atenciosamente,
Financeiro {{ $proposal->property->block->building->project->name }}

Esta é uma mensagem enviada através do sistema MG2 – Incorp.

@endcomponent

@endcomponent
