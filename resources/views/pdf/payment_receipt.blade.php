<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'Laravel') }}</title>
        <style>
            table{border-collapse:collapse}.table{width:100%;margin-bottom:1rem;color:#212529}.table td,.table th{padding:0;vertical-align:top;border-top:1px solid transparent}.table thead th{vertical-align:bottom;border-bottom:2px solid transparent}.table tbody+tbody{border-top:2px solid transparent}.table-sm td,.table-sm th{padding:0}.table-bordered{border:1px solid transparent}.table-bordered td,.table-bordered th{border:1px solid transparent}.table-bordered thead td,.table-bordered thead th{border-bottom-width:2px}.table-borderless tbody+tbody,.table-borderless td,.table-borderless th,.table-borderless thead th{border:0}.table-striped tbody tr:nth-of-type(odd){background-color:rgba(0,0,0,.05)}.table-hover tbody tr:hover{color:#212529;background-color:rgba(0,0,0,.075)}.table-primary,.table-primary>td,.table-primary>th{background-color:#b8daff}.table-primary tbody+tbody,.table-primary td,.table-primary th,.table-primary thead th{border-color:#7abaff}.table-hover .table-primary:hover{background-color:#9fcdff}.table-hover .table-primary:hover>td,.table-hover .table-primary:hover>th{background-color:#9fcdff}.table-secondary,.table-secondary>td,.table-secondary>th{background-color:#d6d8db}.table-secondary tbody+tbody,.table-secondary td,.table-secondary th,.table-secondary thead th{border-color:#b3b7bb}.table-hover .table-secondary:hover{background-color:#c8cbcf}.table-hover .table-secondary:hover>td,.table-hover .table-secondary:hover>th{background-color:#c8cbcf}.table-success,.table-success>td,.table-success>th{background-color:#c3e6cb}.table-success tbody+tbody,.table-success td,.table-success th,.table-success thead th{border-color:#8fd19e}.table-hover .table-success:hover{background-color:#b1dfbb}.table-hover .table-success:hover>td,.table-hover .table-success:hover>th{background-color:#b1dfbb}.table-info,.table-info>td,.table-info>th{background-color:#bee5eb}.table-info tbody+tbody,.table-info td,.table-info th,.table-info thead th{border-color:#86cfda}.table-hover .table-info:hover{background-color:#abdde5}.table-hover .table-info:hover>td,.table-hover .table-info:hover>th{background-color:#abdde5}.table-warning,.table-warning>td,.table-warning>th{background-color:#ffeeba}.table-warning tbody+tbody,.table-warning td,.table-warning th,.table-warning thead th{border-color:#ffdf7e}.table-hover .table-warning:hover{background-color:#ffe8a1}.table-hover .table-warning:hover>td,.table-hover .table-warning:hover>th{background-color:#ffe8a1}.table-danger,.table-danger>td,.table-danger>th{background-color:#f5c6cb}.table-danger tbody+tbody,.table-danger td,.table-danger th,.table-danger thead th{border-color:#ed969e}.table-hover .table-danger:hover{background-color:#f1b0b7}.table-hover .table-danger:hover>td,.table-hover .table-danger:hover>th{background-color:#f1b0b7}.table-light,.table-light>td,.table-light>th{background-color:#fdfdfe}.table-light tbody+tbody,.table-light td,.table-light th,.table-light thead th{border-color:#fbfcfc}.table-hover .table-light:hover{background-color:#ececf6}.table-hover .table-light:hover>td,.table-hover .table-light:hover>th{background-color:#ececf6}.table-dark,.table-dark>td,.table-dark>th{background-color:#c6c8ca}.table-dark tbody+tbody,.table-dark td,.table-dark th,.table-dark thead th{border-color:#95999c}.table-hover .table-dark:hover{background-color:#b9bbbe}.table-hover .table-dark:hover>td,.table-hover .table-dark:hover>th{background-color:#b9bbbe}.table-active,.table-active>td,.table-active>th{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover{background-color:rgba(0,0,0,.075)}.table-hover .table-active:hover>td,.table-hover .table-active:hover>th{background-color:rgba(0,0,0,.075)}.table .thead-dark th{color:#fff;background-color:#343a40;border-color:#454d55}.table .thead-light th{color:#495057;background-color:#e9ecef;border-color:transparent}.table-dark{color:#fff;background-color:#343a40}.table-dark td,.table-dark th,.table-dark thead th{border-color:#454d55}.table-dark.table-bordered{border:0}.table-dark.table-striped tbody tr:nth-of-type(odd){background-color:rgba(255,255,255,.05)}.table-dark.table-hover tbody tr:hover{color:#fff;background-color:rgba(255,255,255,.075)}@media (max-width:575.98px){.table-responsive-sm{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-sm>.table-bordered{border:0}}@media (max-width:767.98px){.table-responsive-md{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-md>.table-bordered{border:0}}@media (max-width:991.98px){.table-responsive-lg{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-lg>.table-bordered{border:0}}@media (max-width:1199.98px){.table-responsive-xl{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive-xl>.table-bordered{border:0}}.table-responsive{display:block;width:100%;overflow-x:auto;-webkit-overflow-scrolling:touch}.table-responsive>.table-bordered{border:0}
            .font-size-1 { font-size: 0.875rem; }
            td, th { vertical-align: middle !important }
            .text-center { text-align: center !important }
            #receipt { border: 1px solid #dee2e6; padding: 1rem }
        </style>
    </head>
    <body>
        <?php
            $valor_original = $billing->value - $billing->extra_value;
            $num_parcela = $billing->payment->billings->pluck('id')->search($billing->id) + 1;
        ?>
        <div id="receipt">
            <table class="table table-sm table-bordered font-size-1">
                <tbody>
                    <tr>
                        <td width="25%">
                            <img src="{{ asset(env('PROJECTS_IMAGES_DIR').$billing->payment->proposal->property->block->building->project->photo) }}" width="100px">
                        </td>
                        <td width="25%"></td>
                        <td width="50%">
                            <table class="table table-sm table-bordered font-size-1">
                                <tbody>
                                    <tr>
                                        <td style="text-align: right;padding-right: 10px">Recibo gerado pelo sistema</td>
                                        <td width="25%"><img src="{{ asset('img/logo2.png') }}"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <table class="table table-sm table-bordered font-size-1">
                <tbody>
                    <tr>
                        <td width="25%">
                            <table class="table table-sm table-bordered font-size-1">
                                <tbody>
                                    <tr>
                                        <td>Vencimento</td>
                                        <td>{{ formatData($billing->expires_at) }} </td>
                                    </tr>
                                    <tr>
                                        <td>Parcela</td>
                                        <td>{{ $num_parcela }} / {{ $billing->payment->quantity }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                        <td width="50%" class="text-center"><h3><b>RECIBO DE PAGAMENTO</b><h3></td>
                        <td width="25%">
                            <table class="table table-sm table-bordered font-size-1">
                                <tbody>
                                    <tr>
                                        <td>Valor original</td>
                                        <td>R$ {{ formatMoney($valor_original) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Acréscimos</td>
                                        <td>R$ {{ formatMoney($billing->extra_value) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Valor corrigido</td>
                                        <td>R$ {{ formatMoney($valor_original + $billing->extra_value) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Desconto</td>
                                        <td>R$ {{ formatMoney($billing->discount_value) }}</td>
                                    </tr>
                                    <tr>
                                        <td><b>Valor cobrado</b></td>
                                        <td><b>R$ {{ formatMoney($billing->paid_value) }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table class="table table-sm table-bordered font-size-1">
                <tbody>
                    <tr>
                        <td width="35%">
                            <div><b>COMPRADOR(A)</b></div>
                            <div>{{ $billing->payment->proposal->main_proponent->name }}</div>
                            <div>CPF/CNPJ: {{ $billing->payment->proposal->main_proponent->document }}</div>
                        </td>
                        <td width="35%">
                            <div><b>VENDEDOR(A)</b></div>
                            <div>{{ $billing->payment->proposal->property->block->building->project->social_name }}</div>
                            <div>CNPJ: {{ $billing->payment->proposal->property->block->building->project->cnpj }}</div>
                        </td>
                        <td width="30%" class="text-center">
                            <?php //setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese'); echo utf8_encode(strftime('%d de %B de %Y', strtotime('today'))); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table class="table table-sm table-bordered font-size-1">
                <tbody>
                    <tr>
                        <td>
                            <p>Recebi(emos) de {{ $billing->payment->proposal->main_proponent->name }} - CPF/CNPJ {{ $billing->payment->proposal->main_proponent->document }} a importância de R$ {{ formatMoney($billing->paid_value) }} ({{ convert_number_to_words($billing->paid_value) }} reais) referente ao pagamento da parcela {{ $num_parcela }} / {{ $billing->payment->quantity }} com vencimento original na data {{ formatData($billing->expires_at) }} do contrato {{ $billing->payment->proposal->id }} - {{ $billing->payment->proposal->property->block->building->project->social_name }} - Bloco {{ $billing->payment->proposal->property->block->building->name }} - Quadra/Andar {{ $billing->payment->proposal->property->block->label }} - Numero {{ $billing->payment->proposal->property->number }}.</p>
                            <p>Para maior clareza firmo o presente recibo para que produza os seus efeitos, dando plena, rasa e irrevogável quitação, pelo valor recebido.</p>
                            <p>Observações: {{ $billing->notes }}</p>
                        </td>
                    </tr>
                </tbody>
            </table>
            <hr>
            <table class="table table-sm table-bordered font-size-1">
                <tbody>
                    <tr>
                        <td>
                            {{ $billing->payment->proposal->property->block->building->project->local }}, <?php setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese'); echo utf8_encode(strftime('%d de %B de %Y', strtotime('today'))); ?>
                        </td>
                    </tr>
                </tbody>
            </table>
            <br>
            <table class="table table-sm table-bordered font-size-1">
                <tbody>
                    <tr>
                        <td width="15%"></td>
                        <td width="70%" class="text-center">
                            <br>
                            <br>
                            <br>
                            <br>
                            <hr>
                            Recebido por: <b>{{ Auth::user()->name }}</b>
                        </td>
                        <td width="15%"></td>
                    </tr>
                </tbody>
            </table>
            <br>
            <br>
            <div class="text-center">
                <i>Sistema MG2 Incorp - www.mg2incorp.com.br (19) 3500.8414</i>
            </div>
        </div>
    </body>
</html>