@extends('layouts.app')
@section('content')

    @if(!Session::has('block_notification') && isset($overdues) && count($overdues))
        <div class="position-fixed" style="bottom: 20px; right: 20px; z-index: 100">
            <div class="toast ml-auto bg-danger" role="alert" data-delay="700" data-autohide="false" id="myToast">
                <div class="toast-header">
                    <strong class="mr-auto text-danger">Financeiro</strong>
                    <button type="button" class="ml-auto mb-1 close" data-dismiss="toast" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="toast-body">
                    <a class="text-white text-decoration-none" href="{{ route('billing.index') }}?status=OVERDUE">Existem clientes inadimplentes no momento.</a>
                </div>
            </div>
        </div>
    @endif

    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Filtros</div>
                    <div class="card-body">
                        <form method="GET" action="">
                            <div class="row">
                                <div class="col-12 col-sm-2">
                                    <div class="form-group mb-sm-0">
                                        <label>Número</label>
                                        <input type="text" name="number" class="form-control" value="{{ @$filters['number'] }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group mb-sm-0">
                                        <label>Empreendimento</label>
                                        <select name="project" class="form-control">
                                            <option value="">Selecione...</option>
                                            <option value="">Todos</option>
                                            @foreach($projects as $project)
                                                <option value="{{ $project->id }}" {{ isset($filters['project']) && $filters['project'] == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group mb-sm-0">
                                        <label>Proponente</label>
                                        <input type="text" name="proponent" class="form-control" value="{{ @$filters['proponent'] }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group mb-sm-0">
                                        <label>Status</label>
                                        <select name="status" class="form-control">
                                            <option value="ALL" {{ isset($filters['status']) && $filters['status'] == 'ALL' ? 'selected' : '' }}>Todos</option>
                                            <option value="ON_DAY" {{ isset($filters['status']) && $filters['status'] == 'ON_DAY' ? 'selected' : '' }}>Em dia</option>
                                            <option value="OVERDUE" {{ isset($filters['status']) && $filters['status'] == 'OVERDUE' ? 'selected' : '' }}>Inadimplente</option>
                                            <option value="FINISH" {{ isset($filters['status']) && $filters['status'] == 'FINISH' ? 'selected' : '' }}>Finalizado</option>
                                            <option value="CANCELED" {{ isset($filters['status']) && $filters['status'] == 'CANCELED' ? 'selected' : '' }}>Cancelado</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2 d-flex flex-column justify-content-end">
                                    <button class="btn btn-success btn-block">Filtrar</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="card">
                    <div class="card-header">Contratos</div>
                    <div class="card-body">
                        @if(count($proposals))
                            <table class="table table-sm table-bordered table-hover text-center" id="tabela">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="25%">Proponente</th>
                                        <th width="25%">Empreendimento</th>
                                        <th>Bloco</th>
                                        <th>Quadra / Andar</th>
                                        <th>Número</th>
                                        <th width="12%">Status</th>
                                        <th width="8%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($proposals as $proposal)
                                        <?php
                                            $status = $proposal->getContractStatus();
                                            $info = getContractStatusLayout($status);
                                        ?>
                                        <tr>
                                            <td>{{ $proposal->id }}</td>
                                            <td>{{ @$proposal->main_proponent->name }}</td>
                                            <td>{{ $proposal->property->block->building->project->name }}</td>
                                            <td>{{ $proposal->property->block->building->name }}</td>
                                            <td>{{ $proposal->property->block->label }}</td>
                                            <td>{{ $proposal->property->number }}</td>
                                            <td class="text-{{ $info['text'] }} font-weight-bold"><i class="{{ $info['icon'] }}"></i> {{ $info['content'] }}</td>
                                            <td>
                                                <div class="btn-group dropleft">
                                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                    <div class="dropdown-menu">
                                                        <h6 class="dropdown-header font-weight-bold text-danger">Pagamentos</h6>
                                                        <a class="dropdown-item" href="{{ route('billing.details', $proposal->id) }}">Visualizar</a>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="d-flex justify-content-center">
                                {{ $proposals->appends($filters)->render() }}
                            </div>
                        @else
                            <h5 class="text-center">Nenhum contrato encontrado.</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $('#myToast').toast('show');

            $('#myToast').on('hide.bs.toast', function () {
                $.ajax({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('notification.close') }}", type: "POST", data: {}, cache: false, processData: true, success: function(data) {} });
            });
        });
    </script>
@endsection