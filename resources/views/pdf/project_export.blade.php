<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <style>
            table{border-collapse:collapse}.table{width:100%;margin-bottom:1rem;color:#212529}.table td,.table th{padding:.75rem;vertical-align:top;border-top:1px solid #dee2e6}.table thead th{vertical-align:bottom;border-bottom:2px solid #dee2e6}.table tbody+tbody{border-top:2px solid #dee2e6}.table-sm td,.table-sm th{padding:.3rem}.table-bordered{border:1px solid #dee2e6}.table-bordered td,.table-bordered th{border:1px solid #dee2e6}.table-bordered thead td,.table-bordered thead th{border-bottom-width:2px}.table-borderless tbody+tbody,.table-borderless td,.table-borderless th,.table-borderless thead th{border:0}.table-striped tbody tr:nth-of-type(odd){background-color:rgba(0,0,0,.05)}.table-hover tbody tr:hover{color:#212529;background-color:rgba(0,0,0,.075)}.table-primary,.table-primary>td,.table-primary>th{background-color:#b8daff}.table-primary tbody+tbody,.table-primary td,.table-primary th,.table-primary thead th{border-color:#7abaff}.table-hover .table-primary:hover{background-color:#9fcdff}.table-hover .table-primary:hover>td,.table-hover .table-primary:hover>th{background-color:#9fcdff}.table-secondary,.table-secondary>td,.table-secondary>th{background-color:#d6d8db}.table-secondary tbody+tbody,.table-secondary td,.table-secondary th,.table-secondary thead th{border-color:#b3b7bb}.table-hover .table-secondary:hover{background-color:#c8cbcf}.table-hover .table-secondary:hover>td,.table-hover .table-secondary:hover>th{background-color:#c8cbcf}.table-success,.table-success>td,.table-success>th{background-color:#c3e6cb}.table-success tbody+tbody,.table-success td,.table-success th,.table-success thead th{border-color:#8fd19e}.table-hover .table-success:hover{background-color:#b1dfbb}.table-hover .table-success:hover>td,.table-hover .table-success:hover>th{background-color:#b1dfbb}.table-info,.table-info>td,.table-info>th{background-color:#bee5eb}.table-info tbody+tbody,.table-info td,.table-info th,.table-info thead th{border-color:#86cfda}.table-hover .table-info:hover{background-color:#abdde5}.table-hover .table-info:hover>td,.table-hover .table-info:hover>th{background-color:#abdde5}.table-warning,.table-warning>td,.table-warning>th{background-color:#ffeeba}.table-warning tbody+tbody,.table-warning td,.table-warning th,.table-warning thead th{border-color:#ffdf7e}.table-hover .table-warning:hover{background-color:#ffe8a1}.table-hover .table-warning:hover>td,.table-hover .table-warning:hover>th{background-color:#ffe8a1}.table-danger,.table-danger>td,.table-danger>th{background-color:#f5c6cb}.table-danger tbody+tbody,.table-danger td,.table-danger th,.table-danger thead th{border-color:#ed969e}.table-hover .table-danger:hover{background-color:#f1b0b7}.table-hover .table-danger:hover>td,.table-hover .table-danger:hover>th{background-color:#f1b0b7}.table-light,.table-light>td,.table-light>th{background-color:#fdfdfe}.table-light tbody+tbody,.table-light td,.table-light th,.table-light thead th{border-color:#fbfcfc}.table-hover .table-light:hover{background-color:#ececf6}.table-hover .table-light:hover>td,.table-hover .table-light:hover>th{background-color:#ececf6}.table-dark,.table-dark>td,.table-dark>th{background-color:#c6c8ca}.table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#95999c}.table-hover .table-dark:hover{background-color:#b9bbbe}.table-hover .table-dark:hover>td,.table-hover .table-dark:hover>th{background-color:#b9bbbe}.table-active,.table-active>td,.table-active>th{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover>td,.table-hover .table-active:hover>th{background-color:rgba(0,0,0,.075)}.table .thead-dark th{color:#fff;background-color:#343a40;border-color:#454d55}.table .thead-light th{color:#495057;background-color:#e9ecef;border-color:#dee2e6}.table-dark{color:#fff;background-color:#343a40}.table-dark td,.table-dark th,.table-dark thead th{border-color:#454d55}.table-dark.table-bordered{border:0}.table-dark.table-striped tbody tr:nth-of-type(odd){background-color:rgba(255,255,255,.05)}.table-dark.table-hover tbody tr:hover{color:#fff;background-color:rgba(255,255,255,.075)}@media (max-width:575.98px){.table-responsive-sm{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-sm>.table-bordered{border:0}}@media (max-width:767.98px){.table-responsive-md{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-md>.table-bordered{border:0}}@media (max-width:991.98px){.table-responsive-lg{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-lg>.table-bordered{border:0}}@media (max-width:1199.98px){.table-responsive-xl{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-xl>.table-bordered{border:0}}.table-responsive{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive>.table-bordered{border:0}
            .font-size-1 { font-size: 0.875rem; }
            td, th { vertical-align: middle !important }
        </style>
    </head>
    <body>
        <table class="table table-sm font-size-1" style="border-top: none">
            <tbody>
                <tr>
                    <td style="border-top: none" width="25%">
                        <img src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->photo) }}" width="100px">
                    </td>
                    <td style="border-top: none" width="25%"></td>
                    <td style="border-top: none" width="50%">
                        <table class="table table-sm font-size-1" style="border-top: none">
                            <tbody>
                                <tr>
                                    <td style="text-align: right;padding-right: 10px;border-top: none">Relatório gerado pelo sistema</td>
                                    <td style="border-top: none" width="25%"><img src="{{ asset('img/logo2.png') }}"></td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        <br>
        <br>
        <div>
            <div><b>Nome: </b>{{ $project->name }}</div>
            <div><b>Previsão de entrega: </b>{{ formatData($project->finish_at) }}</div>
            <div><b>Status: </b>{{ $project->status }}</div>
            <div><b>Local: </b>{{ $project->local }}</div>
            <div><b>Observações: </b>{{ $project->notes }}</div>
        </div>
        <br>
        <br>
        <table class="table table-sm table-bordered m-0 text-center font-size-1">
            <thead>
                <tr>
                    <th>BLOCO</th>
                    <th>QUADRA/ANDAR</th>
                    <th>NUMERO</th>
                    <th>STATUS</th>
                    <th>VALOR</th>
                    <th>AREA</th>
                </tr>
            </thead>
            <tbody>
                @foreach($project->buildings->sortBy('name') as $building)
                    @foreach($building->blocks->sortBy('label') as $block)
                        @foreach($block->properties->sortBy('number') as $property)
                            <tr>
                                <td>{{ $property->block->building->name }}</td>
                                <td>{{ $property->block->label }}</td>
                                <td>{{ $property->number }}</td>
                                <td>
                                    @if($property->situation == 'AVAILABLE')
                                        @if($property->proposals_actives->count() == 0)
                                            Disponível
                                        @else
                                            @if($property->proposals_actives->first()->status == 'SOLD')
                                                Vendido
                                            @else
                                                Análise
                                            @endif
                                        @endif
                                    @else
                                        Bloqueado
                                    @endif
                                </td>
                                <td>R$ {{ formatMoney($property->value) }}</td>
                                <td>{{ $property->size }} m²</td>
                            </tr>
                        @endforeach
                    @endforeach
                @endforeach
            </tbody>
        </table>
        <p>Tabela de Valores para Simples Conferência, favor verificar disponibilidade no sistema MG2 Incorp.</p>
        <p>Valores sujeitos a alteração sem aviso prévio.</p>
    </body>
</html>