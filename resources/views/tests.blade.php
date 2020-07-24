@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/datatables.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/fixedcolumns/3.3.0/css/fixedColumns.dataTables.min.css">

    <link rel="stylesheet" href="{{ asset('css/choices.css') }}">
    <link rel="stylesheet" href="{{ asset('css/tagify.css') }}">
    <style>
        tags { height: 46px !important }
        .tagify tag:hover:not([readonly]) div::before { -webkit-box-shadow: 0 0 0 30px #d3e2e2 inset !important; box-shadow: 0 0 0 30px #d3e2e2 inset !important; }
        .tagify tag>div::before { -webkit-box-shadow: 0 0 0 30px #e5e5e5 inset !important; box-shadow: 0 0 0 30px #e5e5e5 inset !important; }

        .status-badge { white-space: wrap !important; }
        .status-yellow { background-color: #FFEB3B; }
        .status-blue { background-color: #2196F3; }
        .status-grey { background-color: #757575; }
        .status-orange { background-color: #FF9800; }
        .status-purple { background-color: #9C27B0; }
        .status-red { background-color: #F44336; }
        .status-black { background-color: #000000; }

        .font-size-1 {
            font-size: 0.875rem !important;
        }
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                @if(count($proposals))
                    <form method="GET" action="">
                        <div class="card mb-4">
                            <div class="card-header">Filtros</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-2">
                                        <div class="form-group">
                                            <label>Número</label>
                                            <input type="text" name="number" class="form-control" value="{{ @$filters['number'] }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-5">
                                        <div class="form-group">
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
                                    <div class="col-12 col-sm-5">
                                        <div class="form-group">
                                            <label>Proponente</label>
                                            <input type="text" name="proponent" class="form-control" value="{{ @$filters['proponent'] }}">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <div class="form-group mb-sm-0">
                                            <label>Status</label>
                                            <select name="status" class="form-control">
                                                <option value="">Todos</option>
                                                @foreach(getProposalStatus() as $status)
                                                    <option value="{{ $status }}" {{ isset($filters['status']) && $filters['status'] == $status ? 'selected' : '' }}>{{ getProposalStatusName($status) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group mb-sm-0">
                                            <label>Período</label>
                                            <div class="input-group">
                                                <input type="date" name="start" class="form-control" value="{{ isset($filters['start']) ? $filters['start'] : \Carbon\Carbon::today()->toDateString() }}">
                                                <span class="input-group-prepend input-group-append"><span class="input-group-text">a</span></span>
                                                <input type="date" name="end" class="form-control" value="{{ isset($filters['end']) ? $filters['end'] : \Carbon\Carbon::today()->toDateString() }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer d-flex justify-content-end">
                                <button class="btn btn-success px-5">Filtrar</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive-md u-datatable mb-4">
                        <table id="myTable" class="table table-sm text-center font-size-1">
                            <thead>
                                <tr>
                                    <th width="50px" class="font-weight-medium">#</th>
                                    <th width="150px" class="font-weight-medium">Proponente</th>
                                    <th width="150px" class="font-weight-medium">Empreendimento</th>
                                    <th width="50px" class="font-weight-medium">Bloco</th>
                                    <th width="50px" class="font-weight-medium">Quadra/<br>Andar</th>
                                    <th width="50px" class="font-weight-medium">Número</th>
                                    <th width="150px" class="font-weight-medium">Corretor</th>
                                    <th width="100px" class="font-weight-medium">Data</th>
                                    <th width="100px" class="font-weight-medium">Valor Proposta</th>
                                    <th width="100px" class="font-weight-medium">Valor Unidade</th>
                                    <th width="100px" class="font-weight-medium">Status</th>
                                    <th width="50px" class="font-weight-medium"></th>
                                </tr>
                                <tr>
                                    <th class="is_input">#</th>
                                    <th class="is_input">Proponente</th>
                                    <th class="is_input">Empreendimento</th>
                                    <th class="is_input">Bloco</th>
                                    <th class="is_input">Quadra/<br>Andar</th>
                                    <th class="is_input">Número</th>
                                    <th class="is_input">Corretor</th>
                                    <th class="is_input">Data</th>
                                    <th class="is_input">Valor Proposta</th>
                                    <th class="is_input">Valor Unidade</th>
                                    <th class="is_input">Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody class="font-size-1">
                                @foreach($proposals as $proposal)
                                    <tr>
                                        <td>{{ $proposal->id }}</td>
                                        <td>{{ @$proposal->main_proponent->name }}</td>
                                        <td>{{ $proposal->property->block->building->project->name }}</td>
                                        <td>{{ $proposal->property->block->building->name }}</td>
                                        <td>{{ $proposal->property->block->label }}</td>
                                        <td>{{ $proposal->property->number }}</td>
                                        <td>{{ $proposal->user->name }} ({{ @$proposal->user->user_projects->where('project_id', $proposal->property->block->building->project->id)->first()->company->name }})</td>
                                        <td>{{ dateString($proposal->created_at) }}</td>
                                        <td>R$ {{ formatMoney($proposal->payments->sum('total_value')) }}</td>
                                        <td>R$ {{ formatMoney($proposal->property->value) }}</td>
                                        <td>
                                            @switch($proposal->status)
                                                @case('RESERVED') @case('QUEUE_1') @case('QUEUE_2') @php $color = 'status-yellow'; @endphp @break
                                                @case('DOCUMENTS_PENDING') @php $color = 'status-blue'; @endphp @break
                                                @case('PROPOSAL') @case('PROPOSAL_REVIEW') @case('DOCUMENTS_REVIEW') @php $color = 'status-grey'; @endphp @break
                                                @case('CONTRACT_ISSUE') @case('CONTRACT_AVAILABLE') @php $color = 'status-orange'; @endphp @break
                                                @case('PENDING_SIGNATURE_CLIENT') @case('PENDING_SIGNATURE_CONSTRUCTOR') @php $color = 'status-purple'; @endphp @break
                                                @case('SOLD') @php $color = 'status-red'; @endphp @break
                                                @case('REFUSED') @case('CANCELED') @php $color = 'status-black'; @endphp @break
                                            @endswitch
                                            <div class="status-badge text-center text-white {{ @$color }} px-2 py-1">{{ getProposalStatusName($proposal->status) }}</div>
                                        </td>
                                        <td>
                                            <div class="btn-group dropright">
                                                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                <div class="dropdown-menu">
                                                    <h6 class="dropdown-header font-weight-bold text-danger">Proposta</h6>
                                                    <a class="dropdown-item" href="{{ route('proposals.show') }}?proposta={{ $proposal->id }}" target="_BLANK">Visualizar</a>
                                                    <a class="dropdown-item" href="{{ route('proposals.print') }}?proposta={{ $proposal->id }}" target="_BLANK">Emitir</a>
                                                    @if(Auth::user()->checkPermission($proposal->property->block->building->project->id, ['PROPOSAL_EDIT']))
                                                        @if(in_array($proposal->status, ['RESERVED', 'DOCUMENTS_PENDING', 'REFUSED' ]))
                                                            <?
                                                                $libera = true;
                                                                if ($proposal->user_id != Auth::user()->id) {
                                                                    if(Auth::user()->role == 'INCORPORATOR') {
                                                                        if(Auth::user()->constructor_id != $proposal->property->block->building->project->constructor_id) {
                                                                            $libera = false;
                                                                        }
                                                                    }
                                                                    if(Auth::user()->role == 'COORDINATOR') {
                                                                        $companies_ids = App\CompanyProject::where('project_id', $proposal->property->block->building->project->id)->get()->pluck('company_id')->toArray();
                                                                        $user_company = App\UserCompany::where('user_id', Auth::user()->id)->whereIn('company_id', $companies_ids)->where('is_coordinator', 1)->first();

                                                                        if(!$user_company) $libera = false;
                                                                    }
                                                                    if(Auth::user()->role == 'AGENT') $libera = false;
                                                                }
                                                            ?>
                                                            @if($libera)
                                                                <a class="dropdown-item" href="{{ route('proposals.edit', $proposal->id) }}">Editar</a>
                                                            @endif
                                                        @endif
                                                    @endif
                                                    <button class="dropdown-item pointer" type="button" data-toggle="modal" data-target="#docs{{ $proposal->id }}">Documentação</button>
                                                    <div class="dropdown-divider"></div>
                                                    <h6 class="dropdown-header font-weight-bold text-danger">Status</h6>
                                                    @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                                                        @if(!in_array($proposal->status, ['QUEUE_1', 'QUEUE_2']))
                                                            <button class="dropdown-item pointer" type="button" data-toggle="modal" data-target="#status{{ $proposal->id }}">Alterar</button>
                                                        @endif
                                                    @endif
                                                    <button class="dropdown-item pointer" type="button" data-toggle="modal" data-target="#history{{ $proposal->id }}">Histórico</button>
                                                    @if($proposal->file)
                                                        <div class="dropdown-divider"></div>
                                                        <h6 class="dropdown-header font-weight-bold text-danger">Contrato</h6>
                                                        <a class="dropdown-item" href="{{ route('contracts.download', $proposal->file) }}" target="_BLANK">Visualizar/Emitir</a>
                                                        <button class="dropdown-item pointer" type="button" data-toggle="modal" data-target="#send_email{{ $proposal->id }}">Enviar por e-mail</button>
                                                        <button class="dropdown-item pointer" type="button" data-toggle="modal" data-target="#send_wp{{ $proposal->id }}">Enviar por WhatsApp</button>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <h5 class="text-center">Nenhuma proposta encontrada.</h5>
                @endif
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/datatables.js') }}"></script>
    <script src="https://cdn.datatables.net/fixedcolumns/3.3.0/js/dataTables.fixedColumns.min.js"></script>

    <script src="{{ asset('js/choices.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/jquery-tagify.js') }}"></script>
    <script>
        $(document).ready(function() {
            const choices2 = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione os usuários...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar'  });

            $('.tags').each(function() {
                $(this).tagify({ duplicates: false, suggestionsMinChars: 3 });
            });

            $('.dropdown-menu').click(function(e) {
                e.stopPropagation();
                if ($(e.target).is('[data-toggle=modal]')) {
                    $($(e.target).data('target')).modal();
                }
            });

            $('#myTable thead tr:eq(1) th').each( function () {
                var title = $(this).text();
                if ($(this).hasClass('is_input')) {
                    $(this).html('<input type="text" placeholder="buscar" class="column_input form-control form-control-sm">');
                } else {

                }
            } );

            // var table = $('#myTable').DataTable({
            //     language: {
            //         "sEmptyTable": "Nenhum registro encontrado",
            //         "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
            //         "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
            //         "sInfoFiltered": "(Filtrados de _MAX_ registros)",
            //         "sInfoPostFix": "",
            //         "sInfoThousands": ".",
            //         "sLengthMenu": "_MENU_ resultados por página",
            //         "sLoadingRecords": "Carregando...",
            //         "sProcessing": "Processando...",
            //         "sZeroRecords": "Nenhum registro encontrado",
            //         "sSearch": "Pesquisar",
            //         "oPaginate": {
            //             "sNext": "Próximo",
            //             "sPrevious": "Anterior",
            //             "sFirst": "Primeiro",
            //             "sLast": "Último"
            //         },
            //         "oAria": {
            //             "sSortAscending": ": Ordenar colunas de forma ascendente",
            //             "sSortDescending": ": Ordenar colunas de forma descendente"
            //         },
            //         "select": {
            //             "rows": {
            //                 "_": "Selecionado %d linhas",
            //                 "0": "Nenhuma linha selecionada",
            //                 "1": "Selecionado 1 linha"
            //             }
            //         }
            //     },
            //     paging: true,
            //     info: true,
            //     lengthChange: true,
            //     lengthMenu: [ 20, 50, 100 ],
            //     pageLength: 20,
            //     orderCellsTop: true,
            //     fixedHeader: true,
            //     scrollX: true,
            //     ordering: false,
            //     fixedColumns: {
            //         leftColumns: 3,
            //         rightColumns: 2,
            //     },
            // });

            $(document).on('keyup', ".column_input", function (ev) {
                table.column($(this).parent().index()).search(this.value).draw();
            }).on('keydown', ".column_input", function (ev) {
                if (ev.keyCode == 13) {
                    ev.preventDefault();
                    ev.stopPropagation();
                    return false;
                }
            });
        });
    </script>
@endsection


