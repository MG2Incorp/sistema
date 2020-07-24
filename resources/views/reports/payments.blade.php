@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Filtrar</div>
                    <div class="card-body">
                        <form class="row" action="" method="GET">
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label>Período</label>
                                    <div class="input-group">
                                        <input type="date" name="start" class="form-control" value="{{ isset($start) ? $start : \Carbon\Carbon::today()->toDateString() }}">
                                        <span class="input-group-append"><span class="input-group-text">a</span></span>
                                        <input type="date" name="end" class="form-control" value="{{ isset($end) ? $end : \Carbon\Carbon::today()->toDateString() }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label>Método de pagamento</label>
                                    <select name="method" class="form-control" required="required">
                                        <!-- <option value="">Selecione...</option> -->
                                        <option value="ALL" {{ isset($method) && $method == 'ALL' ? 'selected' : '' }}>Todos</option>
                                        <option value="Dinheiro" {{ isset($method) && $method == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                        <option value="Cheque" {{ isset($method) && $method == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="Boleto" {{ isset($method) && $method == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                                        <option value="Cartão de Débito" {{ isset($method) && $method == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                        <option value="Cartão de Crédito" {{ isset($method) && $method == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                        <option value="Nota promissória" {{ isset($method) && $method == 'Nota promissória' ? 'selected' : '' }}>Nota promissória</option>
                                        <option value="Financiamento Bancário" {{ isset($method) && $method == 'Financiamento Bancário' ? 'selected' : '' }}>Financiamento Bancário</option>
                                        <option value="Cheque+Boleto" {{ isset($method) && $method == 'Cheque+Boleto' ? 'selected' : '' }}>Cheque+Boleto</option>
                                        <option value="Transferência Bancária" {{ isset($method) && $method == 'Transferência Bancária' ? 'selected' : '' }}>Transferência Bancária</option>
                                        <option value="Comissão" {{ isset($method) && $method == 'Comissão' ? 'selected' : '' }}>Comissão</option>
                                        <option value="TED/DOC" {{ isset($method) && $method == 'TED/DOC' ? 'selected' : '' }}>TED/DOC</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group">
                                    <label>Empreendimento</label>
                                    <select name="empreendimento" class="form-control" required="required">
                                        <!-- <option value="">Selecione...</option> -->
                                        <option value="ALL" {{ isset($empreendimento) && $empreendimento == 'ALL' ? 'selected' : '' }}>Todos</option>
                                        @foreach($projects as $proj)
                                            <option value="{{ $proj->id }}" {{ isset($empreendimento) && $empreendimento == $proj->id ? 'selected' : '' }}>{{ $proj->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 text-right">
                                <input type="submit" name="search" class="btn btn-primary" value="Buscar">
                                <input type="submit" name="export" class="btn btn-success ml-auto" value="Exportar">
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Gerenciar pagamentos</div>
                    <div class="card-body">
                        @if($payments->count())
                            <table class="table table-hover table-bordered table-sm mb-2 text-center">
                                <thead>
                                    <tr>
                                        <th width="5%">Nº</th>
                                        <th width="8%">Vencimento</th>
                                        <th>Proponente</th>
                                        <th>Empreendimento</th>
                                        <th width="12%">Corretor</th>
                                        <th width="12%">Imobiliária</th>
                                        <th>Método</th>
                                        <th width="10%">Valor À Vista</th>
                                        <th width="10%">Valor Contrato</th>
                                        <th width="10%">Valor Parcela</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($payments as $payment)
                                        <?php

                                            $inicio = Carbon\Carbon::parse(dataToSQL($start))->startOfDay();
                                            $fim = Carbon\Carbon::parse(dataToSQL($end))->endOfDay();

                                            switch ($payment->component) {
                                                case 'Anual': $param = 12; break;
                                                case 'Semestre': $param = 6; break;
                                                case 'Trimestral': $param = 3; break;
                                                case 'Bimestral': $param = 2; break;
                                                case 'Mensal': case 'Cartão de crédito': case 'Financiamento': $param = 1; break;
                                                default: $param = 1;
                                            }

                                            for ($i = 0; $i < $payment->quantity; $i++) {
                                                $aux = Carbon\Carbon::parse($payment->expires_at);
                                                $parcela = $aux->addMonths($param*$i);

                                                if ($parcela->between($inicio, $fim)) { ?>
                                                    <tr>
                                                        <td>#{{ $payment->proposal_id }}</td>
                                                        <td>{{ dateString($parcela) }}</td>
                                                        <td>{{ $payment->proposal->main_proponent->name }}</td>
                                                        <td>{{ @$payment->proposal->property->block->building->project->name }}</td>
                                                        <td>{{ @$payment->proposal->user->name }}</td>
                                                        <td>{{ @$payment->proposal->user->user_projects->where('project_id', $payment->proposal->property->block->building->project->id)->first()->company->name }}</td>
                                                        <td>{{ $payment->method }}</td>
                                                        <td>R$ {{ formatMoney($payment->proposal->property->value) }}</td>
                                                        <td>R$ {{ formatMoney($payment->proposal->payments->sum('total_value')) }}</td>
                                                        <td>R$ {{ formatMoney($payment->unit_value) }}</td>
                                                    </tr>
                                        <?php   }
                                            }
                                        ?>
                                    @endforeach
                                </tbody>
                            </table>

                            <table class="table table-bordered table-sm m-0 text-right">
                                <thead>
                                    <tr class="invisible">
                                        <th width="8%"></th>
                                        <th width="8%"></th>
                                        <th></th>
                                        <th></th>
                                        <th width="12%"></th>
                                        <th width="12%"></th>
                                        <th width="10%"></th>
                                        <th width="10%"></th>
                                        <th width="10%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td colspan="7"><b>TOTAL CONTRATOS (À VISTA)</b></td>
                                        <td colspan="2" class="text-center"><b>R$ {{ formatMoney($payments->sum(function($payment) { return $payment->proposal->property->value; })) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"><b>TOTAL CONTRATOS (À PRAZO)</b></td>
                                        <td colspan="2" class="text-center"><b>R$ {{ formatMoney($payments->sum(function($payment) { return $payment->proposal->payments->sum('total_value'); })) }}</b></td>
                                    </tr>
                                    <tr>
                                        <td colspan="7"><b>TOTAL DAS PARCELAS</b></td>
                                        <td colspan="2" class="text-center"><b>R$ {{ formatMoney($payments->sum('unit_value')) }}</b></td>
                                    </tr>
                                </tbody>
                            </table>
                        @else
                            <h5 class="text-center m-0">Nenhum pagamento encontrado com os filtros escolhidos.</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection