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
                    <td style="border-top: none" width="50%">

                    </td>
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
        <h2 style="margin: 0 !important;text-align: center !important">Relatório de Cobranças</h2>
        <br>
        @if(isset($filters['start'])) <div>Período do Vencimento - Início: {{ $filters['start'] }}</div> @endif
        @if(isset($filters['end'])) <div>Período do Vencimento - Final: {{ $filters['end'] }}</div> @endif
        @if(isset($filters['project'])) <div>Empreendimento: {{ $filters['project'] }}</div> @endif
        @if(isset($filters['status'])) <div>Status: {{ $filters['status'] }}</div> @endif
        @if(isset($filters['method'])) <div>Metódo: {{ $filters['method'] }}</div> @endif
        <br>
        <table class="table table-sm table-bordered m-0 text-center font-size-1">
            <thead>
                <tr>
                    <th>Contrato</th>
                    <th>Vencimento</th>
                    <th>Proponente</th>
                    <th>Empreendimento</th>
                    <th>Método</th>
                    <th>Data do Pagamento</th>
                    <th>Status</th>
                    <th>Valor Cobrança</th>
                    <th>Valor Pago</th>
                </tr>
            </thead>
            <tbody>
                @foreach($billings as $billing)
                    <?php $stat = getBillingStatusLayout($billing->status); ?>
                    <tr>
                        <td>{{ $billing->payment->proposal_id }}</td>
                        <td>{{ formatData($billing->expires_at) }}</td>
                        <td>{{ $billing->payment->proposal->main_proponent->name }}</td>
                        <td>{{ $billing->payment->proposal->property->block->building->project->name }}</td>
                        <td>{{ $billing->payment->method }}</td>
                        <th>{{ $billing->getPaymentDate() }}</th>
                        <td>{{ $stat['content'] }}</td>
                        <td>R$ {{ formatMoney($billing->value) }}</td>
                        <td>R$ {{ formatMoney($billing->paid_value) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <td colspan="6"></td>
                </tr>
                @if(in_array($status, [ 'PENDING', 'ALL' ]))
                    <tr>
                        <td colspan="6"></td>
                        <td>Total Pendente</td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PENDING')->sum('value')) }}</b></td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PENDING')->sum('paid_value')) }}</b></td>
                    </tr>
                @endif
                @if(in_array($status, [ 'PAID', 'PAID_MANUAL', 'ALL' ]))
                    <tr>
                        <td colspan="6"></td>
                        <td>Total Pago</td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PAID')->sum('value') + $billings->where('status', 'PAID_MANUAL')->sum('value')) }}</b></td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PAID')->sum('paid_value') + $billings->where('status', 'PAID_MANUAL')->sum('paid_value')) }}</b></td>
                    </tr>
                @endif
                <!-- @if(in_array($status, [ 'PAID_MANUAL', 'ALL' ]))
                    <tr>
                        <td colspan="5"></td>
                        <td>Pago Manual</td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PAID_MANUAL')->sum('value')) }}</b></td>
                    </tr>
                @endif -->
                @if(in_array($status, [ 'CANCELED', 'ALL' ]))
                    <tr>
                        <td colspan="6"></td>
                        <td>Total Cancelado</td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'CANCELED')->sum('value')) }}</b></td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'CANCELED')->sum('paid_value')) }}</b></td>
                    </tr>
                @endif
                @if(in_array($status, [ 'OUTDATED', 'ALL' ]))
                    <tr>
                        <td colspan="6"></td>
                        <td>Total Vencido</td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'OUTDATED')->sum('value')) }}</b></td>
                        <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'OUTDATED')->sum('paid_value')) }}</b></td>
                    </tr>
                @endif
            </tbody>
        </table>
    </body>
</html>