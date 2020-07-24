@extends('layouts.app')
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <form action="" method="GET">
                    <div class="card mb-4">
                        <div class="card-header">Filtrar</div>
                        <div class="card-body row">
                            <div class="col-12 col-sm-5">
                                <div class="form-group">
                                    <label>Data de Vencimento</label>
                                    <div class="input-group">
                                        <input type="date" name="start" class="form-control" value="{{ isset($filters['start']) ? $filters['start'] : \Carbon\Carbon::today()->toDateString() }}">
                                        <span class="input-group-prepend input-group-append"><span class="input-group-text">a</span></span>
                                        <input type="date" name="end" class="form-control" value="{{ isset($filters['end']) ? $filters['end'] : \Carbon\Carbon::today()->toDateString() }}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-4">
                                <div class="form-group mb-sm-0">
                                    <label>Empreendimento</label>
                                    <select name="project" class="form-control">
                                        <option value="">Selecione...</option>
                                        <option value="">Todos</option>
                                        @if(isset($projects))
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" {{ isset($filters['project']) && $filters['project'] == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group mb-sm-0">
                                    <label>Status</label>
                                    <select name="status" class="form-control">
                                        <option value="ALL" {{ isset($filters['status']) && $filters['status'] == 'ALL' ? 'selected' : '' }}>Todos</option>
                                        <option value="PENDING" {{ isset($filters['status']) && $filters['status'] == 'PENDING' ? 'selected' : '' }}>Pendente</option>
                                        <option value="PAID" {{ isset($filters['status']) && $filters['status'] == 'PAID' ? 'selected' : '' }}>Pago</option>
                                        <option value="PAID_MANUAL" {{ isset($filters['status']) && $filters['status'] == 'PAID_MANUAL' ? 'selected' : '' }}>Pago Manual</option>
                                        <option value="CANCELED" {{ isset($filters['status']) && $filters['status'] == 'CANCELED' ? 'selected' : '' }}>Cancelado</option>
                                        <option value="OUTDATED" {{ isset($filters['status']) && $filters['status'] == 'OUTDATED' ? 'selected' : '' }}>Vencido</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-3">
                                <div class="form-group">
                                    <label>Método de pagamento</label>
                                    <select name="method" class="form-control" required="required">
                                        <option value="ALL" {{ isset($filters['method']) && $filters['method'] == 'ALL' ? 'selected' : '' }}>Todos</option>
                                        <option value="Dinheiro" {{ isset($filters['method']) && $filters['method'] == 'Dinheiro' ? 'selected' : '' }}>Dinheiro</option>
                                        <option value="Cheque" {{ isset($filters['method']) && $filters['method'] == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                        <option value="Boleto" {{ isset($filters['method']) && $filters['method'] == 'Boleto' ? 'selected' : '' }}>Boleto</option>
                                        <option value="Cartão de Débito" {{ isset($filters['method']) && $filters['method'] == 'Cartão de Débito' ? 'selected' : '' }}>Cartão de Débito</option>
                                        <option value="Cartão de Crédito" {{ isset($filters['method']) && $filters['method'] == 'Cartão de Crédito' ? 'selected' : '' }}>Cartão de Crédito</option>
                                        <option value="Nota promissória" {{ isset($filters['method']) && $filters['method'] == 'Nota promissória' ? 'selected' : '' }}>Nota promissória</option>
                                        <option value="Financiamento Bancário" {{ isset($filters['method']) && $filters['method'] == 'Financiamento Bancário' ? 'selected' : '' }}>Financiamento Bancário</option>
                                        <option value="Cheque+Boleto" {{ isset($filters['method']) && $filters['method'] == 'Cheque+Boleto' ? 'selected' : '' }}>Cheque+Boleto</option>
                                        <option value="Transferência Bancária" {{ isset($filters['method']) && $filters['method'] == 'Transferência Bancária' ? 'selected' : '' }}>Transferência Bancária</option>
                                        <option value="Comissão" {{ isset($filters['method']) && $filters['method'] == 'Comissão' ? 'selected' : '' }}>Comissão</option>
                                        <option value="TED/DOC" {{ isset($filters['method']) && $filters['method'] == 'TED/DOC' ? 'selected' : '' }}>TED/DOC</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer d-flex justify-content-end">
                            <div class="dropdown dropleft">
                                <button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Exportar</button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                    <input type="submit" name="export" class="dropdown-item" value="XLSX">
                                    <input type="submit" name="export" class="dropdown-item" value="PDF">
                                </div>
                            </div>
                            <input type="submit" name="search" class="btn btn-primary ml-2" value="Buscar">
                        </div>
                    </div>
                </form>
                <div class="card">
                    <div class="card-header d-flex justify-content-between">Quadro de cobranças <span>{{ $billings->count() ? $billings->count().' resultados encontrados' : ''  }}</span></div>
                    <div class="card-body">
                        @if($billings->count())
                            <table class="table table-bordered table-striped table-sm text-center m-0">
                                <thead>
                                    <tr>
                                        <th width="10%">Contrato</th>
                                        <th width="10%">Vencimento</th>
                                        <th>Proponente</th>
                                        <th>Empreendimento</th>
                                        <th width="10%">Método</th>
                                        <th width="10%">Data do Pagamento</th>
                                        <th>Status</th>
                                        <th width="10%">Valor Cobrança</th>
                                        <th width="10%">Valor Pago</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($billings as $billing)
                                        <?php $status = getBillingStatusLayout($billing->status); ?>
                                        <tr>
                                            <td><span class="d-none">{{ $billing->id }}</span>{{ $billing->payment->proposal_id }}</td>
                                            <td>{{ formatData($billing->expires_at) }}</td>
                                            <td>{{ $billing->payment->proposal->main_proponent->name }}</td>
                                            <td>{{ $billing->payment->proposal->property->block->building->project->name }}</td>
                                            <td>{{ $billing->payment->method }}</td>
                                            <th>{{ $billing->getPaymentDate() }}</th>
                                            <td class="text-{{ $status['text'] }} font-weight-bold"><i class="{{ $status['icon'] }}"></i> {{ $status['content'] }}</td>
                                            <td>R$ {{ formatMoney($billing->value) }}</td>
                                            <td>R$ {{ formatMoney($billing->paid_value) }}</td>
                                        </tr>
                                    @endforeach
                                    <td>
                                        <td colspan="6"></td>
                                    </td>
                                    @if(!isset($filters['status']) || in_array($filters['status'], [ 'PENDING', 'ALL' ]))
                                        <tr>
                                            <td colspan="6"></td>
                                            <td>Total Pendente</td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PENDING')->sum('value')) }}</b></td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PENDING')->sum('paid_value')) }}</b></td>
                                        </tr>
                                    @endif
                                    @if(!isset($filters['status']) || in_array($filters['status'], [ 'PAID', 'PAID_MANUAL', 'ALL' ]))
                                        <tr>
                                            <td colspan="6"></td>
                                            <td>Total Pago</td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PAID')->sum('value') + $billings->where('status', 'PAID_MANUAL')->sum('value')) }}</b></td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PAID')->sum('paid_value') + $billings->where('status', 'PAID_MANUAL')->sum('paid_value')) }}</b></td>
                                        </tr>
                                    @endif
                                    <!-- @if(!isset($filters['status']) || in_array($filters['status'], [ 'PAID_MANUAL', 'ALL' ]))
                                        <tr>
                                            <td colspan="5"></td>
                                            <td>Pago Manual</td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'PAID_MANUAL')->sum('value')) }}</b></td>
                                        </tr>
                                    @endif -->
                                    @if(!isset($filters['status']) || in_array($filters['status'], [ 'CANCELED', 'ALL' ]))
                                        <tr>
                                            <td colspan="6"></td>
                                            <td>Total Cancelado</td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'CANCELED')->sum('value')) }}</b></td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'CANCELED')->sum('paid_value')) }}</b></td>
                                        </tr>
                                    @endif
                                    @if(!isset($filters['status']) || in_array($filters['status'], [ 'OUTDATED', 'ALL' ]))
                                        <tr>
                                            <td colspan="6"></td>
                                            <td>Total Vencido</td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'OUTDATED')->sum('value')) }}</b></td>
                                            <td class="text-center"><b>R$ {{ formatMoney($billings->where('status', 'OUTDATED')->sum('paid_value')) }}</b></td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        @else
                            <h5 class="text-center m-0">Nenhuma cobrança encontrada nos filtros escolhidos.</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection