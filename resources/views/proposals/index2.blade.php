@extends('layouts.app')
@section('css')
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
    </style>
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
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
                                            <input type="date" name="start" class="form-control" value="{{ isset($filters['start']) ? $filters['start'] : \Carbon\Carbon::today()->subYear()->toDateString() }}">
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
                @if(count($proposals))
                    <table class="table table-sm table-bordered table-hover bg-light text-center" style="font-size: 13px" id="tabela">
                        <thead>
                            <tr>
                                <th width="3%">#</th>
                                <th>Proponente</th>
                                <th>Empreendimento</th>
                                <th width="5%">Bloco</th>
                                <th width="5%">Quadra/<br>Andar</th>
                                <th width="5%">Número</th>
                                <th>Corretor</th>
                                <th width="8%">Data</th>
                                <th width="8%">Valor Proposta</th>
                                <th width="8%">Valor Unidade</th>
                                <th>Status</th>
                                <th width="8%"></th>
                            </tr>
                            <tr>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                                <th><input type="text" name="" id="" class="form-control" placeholder="Filtrar..."></th>
                            </tr>
                        </thead>
                        <tbody>
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
                                        <div class="btn-group dropleft">
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
                                                <button class="dropdown-item dropdown-menu2 pointer" type="button" data-toggle="modal" data-target="#docs{{ $proposal->id }}">Documentação</button>
                                                <div class="dropdown-divider"></div>
                                                <h6 class="dropdown-header font-weight-bold text-danger">Status</h6>
                                                @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                                                    @if(!in_array($proposal->status, ['QUEUE_1', 'QUEUE_2']))
                                                        <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_load_status" data-id="{{ $proposal->id }}">Alterar</a>
                                                    @endif
                                                @endif
                                                <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_load_history" data-id="{{ $proposal->id }}">Histórico</a>

                                                @if($proposal->file)
                                                    <div class="dropdown-divider"></div>
                                                    <h6 class="dropdown-header font-weight-bold text-danger">Contrato</h6>
                                                    <a class="dropdown-item" href="{{ route('contracts.download', $proposal->file) }}" target="_BLANK">Visualizar/Emitir</a>
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_load_send_email" data-id="{{ $proposal->id }}">Enviar por e-mail</a>
                                                    <a class="dropdown-item" href="#" data-toggle="modal" data-target="#modal_load_send_wp" data-id="{{ $proposal->id }}">Enviar por WhatsApp</a>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center">
                        {{ $proposals->appends($filters)->links() }}
                    </div>
                @else
                    <h5 class="text-center">Nenhuma proposta encontrada.</h5>
                @endif
            </div>
        </div>
    </div>

    @if(count($proposals))
        @foreach($proposals as $proposal)
            <div class="modal fade" id="docs{{ $proposal->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">Documentação - Proposta #{{ $proposal->id }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{ action('ProposalController@document') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                                <ul class="nav nav-tabs d-flex" id="myTab" role="tablist">
                                    @foreach($proposal->proponents as $key => $proponent)
                                        <li class="nav-item"><a class="nav-link {{ $key == 0 ? 'active' : '' }}" data-toggle="tab" href="#tab{{ $proponent->id }}" role="tab" aria-controls="tab{{ $proponent->id }}" aria-selected="true">Proponente {{ $key+1 }}</a></li>
                                        @if($proponent->proponent)
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab{{ $proponent->proponent->id }}" role="tab" aria-controls="tab{{ $proponent->proponent->id }}" aria-selected="true">Cônjuge {{ $key+1 }}</a></li>
                                        @endif
                                    @endforeach
                                    <li class="nav-item float-right ml-auto"><button type="submit" class="btn btn-success">Salvar</button></li>
                                </ul>
                                <div class="tab-content">
                                    @foreach($proposal->proponents as $key => $proponent)
                                        <div class="tab-pane {{ $key == 0 ? 'active' : '' }}" id="tab{{ $proponent->id }}" role="tabpanel" aria-labelledby="home-tab">
                                            <div class="card border-top-0">
                                                <div class="card-body">
                                                    <table class="table table-sm table-bordered text-center">
                                                        <thead>
                                                            <tr>
                                                                <th width="25%">Tipo</th>
                                                                <th>
                                                                    <div>Escolher arquivo</div>
                                                                    <div class="text-danger">Formatos suportados: 'gif', 'bmp', 'png', 'jpg', 'jpeg', 'pdf', 'rar', 'zip', 'html', 'txt', 'tar', 'docx'</div>
                                                                </th>
                                                                <th width="15%">Arquivo já enviado</th>
                                                                <th width="10%">Remover</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody id="table_documents{{ $proponent->id }}">
                                                            <tr>
                                                                <td>RG</td>
                                                                @if($doc = $proponent->documents->where('type', 'rg')->first())
                                                                    <td></td>
                                                                    <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                @else
                                                                    <td><input type="file" name="documents[{{ $proponent->id }}][rg]"></td>
                                                                    <td></td>
                                                                @endif
                                                                <td>
                                                                    @if($proposal->status != 'SOLD')
                                                                        @if($doc = $proponent->documents->where('type', 'rg')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>CPF</td>
                                                                @if($doc = $proponent->documents->where('type', 'cpf')->first())
                                                                    <td></td>
                                                                    <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                @else
                                                                    <td><input type="file" name="documents[{{ $proponent->id }}][cpf]"></td>
                                                                    <td></td>
                                                                @endif
                                                                <td>
                                                                    @if($proposal->status != 'SOLD')
                                                                        @if($doc = $proponent->documents->where('type', 'cpf')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Comprovante de Residência</td>
                                                                @if($doc = $proponent->documents->where('type', 'house')->first())
                                                                    <td></td>
                                                                    <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                @else
                                                                    <td><input type="file" name="documents[{{ $proponent->id }}][house]"></td>
                                                                    <td></td>
                                                                @endif
                                                                <td>
                                                                    @if($proposal->status != 'SOLD')
                                                                        @if($doc = $proponent->documents->where('type', 'house')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Comprovante de Renda</td>
                                                                @if($doc = $proponent->documents->where('type', 'income')->first())
                                                                    <td></td>
                                                                    <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                @else
                                                                    <td><input type="file" name="documents[{{ $proponent->id }}][income]"></td>
                                                                    <td></td>
                                                                @endif
                                                                <td>
                                                                    @if($proposal->status != 'SOLD')
                                                                        @if($doc = $proponent->documents->where('type', 'income')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Comprovante de Estado Civil</td>
                                                                @if($doc = $proponent->documents->where('type', 'birth')->first())
                                                                    <td></td>
                                                                    <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                @else
                                                                    <td><input type="file" name="documents[{{ $proponent->id }}][birth]"></td>
                                                                    <td></td>
                                                                @endif
                                                                <td>
                                                                    @if($proposal->status != 'SOLD')
                                                                        @if($doc = $proponent->documents->where('type', 'birth')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                    @endif
                                                                </td>
                                                            </tr>
                                                            @foreach($proponent->documents_attach as $attach)
                                                                <tr>
                                                                    <td>{{ $attach->text }}</td>
                                                                    <td></td>
                                                                    <td><a href="{{ route('proposals.download', $attach->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                    <td>
                                                                        @if($proposal->status != 'SOLD')
                                                                            <a href="{{ route('proposals.document.delete', $attach->id) }}?proposta={{ $proposal->id }}">Remover</a>
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td>
                                                                    <div class="form-inline">
                                                                        <button type="button" class="btn btn-danger remove_anexo mr-2"><i class="fas fa-times"></i></button>
                                                                        <input type="text" name="other_documents[{{ $proponent->id }}][outro_name][]" placeholder="Outro" class="form-control">
                                                                    </div>
                                                                </td>
                                                                <td><input type="file" name="other_documents[{{ $proponent->id }}][outro_file][]"></td>
                                                                <td></td>
                                                                <td></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <button type="button" class="btn btn-info add_anexo" data-id="{{ $proponent->id }}">Adicionar anexo</button>
                                                </div>
                                            </div>
                                        </div>
                                        @if($proponent->proponent)
                                            <div class="tab-pane" id="tab{{ $proponent->proponent->id }}" role="tabpanel">
                                                <div class="card border-top-0">
                                                    <div class="card-body">
                                                        <table class="table table-sm table-bordered text-center">
                                                            <thead>
                                                                <tr>
                                                                    <th width="25%">Tipo</th>
                                                                    <th>
                                                                        <div>Escolher arquivo</div>
                                                                        <div class="text-danger">Formatos suportados: 'gif', 'bmp', 'png', 'jpg', 'jpeg', 'pdf', 'rar', 'zip', 'html', 'txt', 'tar', 'docx'</div>
                                                                    </th>
                                                                    <th width="15%">Arquivo já enviado</th>
                                                                    <th width="10%">Remover</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody id="table_documents{{ $proponent->proponent->id }}">
                                                                <tr>
                                                                    <td>RG</td>
                                                                    @if($doc = $proponent->proponent->documents->where('type', 'rg')->first())
                                                                        <td></td>
                                                                        <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                    @else
                                                                        <td><input type="file" name="documents[{{ $proponent->proponent->id }}][rg]"></td>
                                                                        <td></td>
                                                                    @endif
                                                                    <td>
                                                                        @if($proposal->status != 'SOLD')
                                                                            @if($doc = $proponent->proponent->documents->where('type', 'rg')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>CPF</td>
                                                                    @if($doc = $proponent->proponent->documents->where('type', 'cpf')->first())
                                                                        <td></td>
                                                                        <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                    @else
                                                                        <td><input type="file" name="documents[{{ $proponent->proponent->id }}][cpf]"></td>
                                                                        <td></td>
                                                                    @endif
                                                                    <td>
                                                                        @if($proposal->status != 'SOLD')
                                                                            @if($doc = $proponent->proponent->documents->where('type', 'cpf')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Comprovante de Residência</td>
                                                                    @if($doc = $proponent->proponent->documents->where('type', 'house')->first())
                                                                        <td></td>
                                                                        <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                    @else
                                                                        <td><input type="file" name="documents[{{ $proponent->proponent->id }}][house]"></td>
                                                                        <td></td>
                                                                    @endif
                                                                    <td>
                                                                        @if($proposal->status != 'SOLD')
                                                                            @if($doc = $proponent->proponent->documents->where('type', 'house')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Comprovante de Renda</td>
                                                                    @if($doc = $proponent->proponent->documents->where('type', 'income')->first())
                                                                        <td></td>
                                                                        <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                    @else
                                                                        <td><input type="file" name="documents[{{ $proponent->proponent->id }}][income]"></td>
                                                                        <td></td>
                                                                    @endif
                                                                    <td>
                                                                        @if($proposal->status != 'SOLD')
                                                                            @if($doc = $proponent->proponent->documents->where('type', 'income')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                <tr>
                                                                    <td>Comprovante de Estado Civil</td>
                                                                    @if($doc = $proponent->proponent->documents->where('type', 'birth')->first())
                                                                        <td></td>
                                                                        <td><a href="{{ route('proposals.download', $doc->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                    @else
                                                                        <td><input type="file" name="documents[{{ $proponent->proponent->id }}][birth]"></td>
                                                                        <td></td>
                                                                    @endif
                                                                    <td>
                                                                        @if($proposal->status != 'SOLD')
                                                                            @if($doc = $proponent->proponent->documents->where('type', 'birth')->first()) <a href="{{ route('proposals.document.delete', $doc->id) }}?proposta={{ $proposal->id }}">Remover</a> @endif
                                                                        @endif
                                                                    </td>
                                                                </tr>
                                                                @foreach($proponent->proponent->documents_attach as $attach)
                                                                    <tr>
                                                                        <td>{{ $attach->text }}</td>
                                                                        <td></td>
                                                                        <td><a href="{{ route('proposals.download', $attach->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a></td>
                                                                        <td>
                                                                            @if($proposal->status != 'SOLD')
                                                                                <a href="{{ route('proposals.document.delete', $attach->id) }}?proposta={{ $proposal->id }}">Remover</a>
                                                                            @endif
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                                <tr>
                                                                    <td>
                                                                        <div class="form-inline">
                                                                            <button type="button" class="btn btn-danger remove_anexo mr-2"><i class="fas fa-times"></i></button>
                                                                            <input type="text" name="other_documents[{{ $proponent->id }}][outro_name][]" placeholder="Outro" class="form-control">
                                                                        </div>
                                                                    </td>
                                                                    <td><input type="file" name="other_documents[{{ $proponent->id }}][outro_file][]"></td>
                                                                    <td></td>
                                                                    <td></td>
                                                                </tr>
                                                            </tbody>
                                                        </table>
                                                        <button type="button" class="btn btn-info add_anexo" data-id="{{ $proponent->proponent->id }}">Adicionar anexo</button>
                                                    </div>
                                                </div>
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif

    <div class="modal fade" id="modal_load_history" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Histórico da Proposta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_modal_load_history"></div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_load_send_email" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form class="modal-content" id="form_send_contract_email">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar contrato por e-mail</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_modal_load_send_email"></div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Enviar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_load_send_wp" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl" role="document">
            <form class="modal-content" id="form_send_contract_phone">
                <div class="modal-header">
                    <h5 class="modal-title">Enviar contrato por WhatsApp</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_modal_load_send_wp"></div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="submit" class="btn btn-success">Gerar links</button>
                </div>
            </form>
        </div>
    </div>

    <div class="modal fade" id="modal_load_status" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Alterar status - Proposta</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" id="content_modal_load_status"></div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/choices.js') }}?v={{ time() }}"></script>
    <script src="{{ asset('js/jquery-tagify.js') }}"></script>
    <script>
        $(document).ready(function() {
            const choices2 = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione os usuários...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar'  });

            $('.tags').each(function() {
                $(this).tagify({ duplicates: false, suggestionsMinChars: 3 });
            });

            $('.dropdown-menu2').click(function(e) {
                e.stopPropagation();
                if ($(e.target).is('[data-toggle=modal]')) {
                    $($(e.target).data('target')).modal();
                }
            });

            $(document).on('change', '.select_status', function() {
                if($(this).val() == 'CONTRACT_ISSUE') {
                    $(this).closest('form').find('.div_modality').removeClass('d-none');
                } else {
                    $(this).closest('form').find('.div_modality').addClass('d-none');
                }

                if($(this).val() == 'CANCELED') {
                    $(this).closest('form').find('#require_pass').removeClass('d-none');
                    $(this).closest('form').find('#input_require_pass').prop('required', true);
                } else {
                    $(this).closest('form').find('#require_pass').addClass('d-none');
                    $(this).closest('form').find('#input_require_pass').prop('required', false);
                }
            });

            $("#form_change_status").validate({
                rules: {
                    status: { required: true, normalizer: function(value) { return $.trim(value); } },
                },
                submitHandler: function(form) { form.submit(); }
            });

            $("#form_send_contract_email").validate({
                submitHandler: function(form) {
                    load();
                    var id = $(form).attr('id');
                    var formulario = document.getElementById(id);
                    var formData = new FormData(formulario);

                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('contracts.send') }}", type: 'POST', data: formData, processData: false, contentType: false, success: function(data) {
                            $("#resultado-form_send_contract_email").html(data);
                            load();
                        }
                    });
                }
            });

            $("#form_send_contract_phone").validate({
                submitHandler: function(form) {
                    load();
                    var id = $(form).attr('id');
                    var formulario = document.getElementById(id);
                    var formData = new FormData(formulario);

                    $.ajax({
                        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('contracts.send') }}", type: 'POST', data: formData, processData: false, contentType: false, success: function(data) {
                            $("#resultado-"+id).html(data);
                            load();
                        }
                    });
                }
            });

            $(document).on('click', '.add_anexo', function() {
                var id = $(this).attr('data-id');
                var clone = $("#table_documents"+id).find('tr').last().clone();
                clone.find('input').each(function(){
                    $(this).val(null);
                });
                $("#table_documents"+id).append(clone);
            });

            $(document).on('click', '.remove_anexo', function() {
                $(this).parent().parent().parent().remove();
            });

            $("#tabela input").keyup(function(){
                var index = $(this).parent().index();
                var nth = "#tabela td:nth-child("+(index+1).toString()+")";
                var valor = $(this).val().toUpperCase();
                $("#tabela tbody tr").show();
                $(nth).each(function(){
                    if($(this).text().toUpperCase().indexOf(valor) < 0){
                        $(this).parent().hide();
                    }
                });
            });

            $('#modal_load_history').on('show.bs.modal', function (event) {
                $("#content_modal_load_history").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                var button = $(event.relatedTarget);
                var id = button.data('id');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('proposals.carregar.historico') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_modal_load_history").html(data);
                    },
                });
            });

            $('#modal_load_status').on('show.bs.modal', function (event) {
                $("#content_modal_load_status").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                var button = $(event.relatedTarget);
                var id = button.data('id');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('proposals.carregar.status') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_modal_load_status").html(data);
                        $("#form_change_status").validate({
                            rules: {
                                status: { required: true, normalizer: function(value) { return $.trim(value); } },
                            },
                            submitHandler: function(form) { form.submit(); }
                        });

                    },
                });
            });

            $('#modal_load_send_email').on('show.bs.modal', function (event) {
                $("#content_modal_load_send_email").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                var button = $(event.relatedTarget);
                var id = button.data('id');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('proposals.carregar.emails') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_modal_load_send_email").html(data);

                        const choices2 = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione os usuários...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar'  });

                        $('.tags').each(function() {
                            $(this).tagify({ duplicates: false, suggestionsMinChars: 3 });
                        });
                    },
                });
            });

            $('#modal_load_send_wp').on('show.bs.modal', function (event) {
                $("#content_modal_load_send_wp").html('<div class="d-flex justify-content-center py-5"><div class="spinner-border" role="status"><span class="sr-only">Loading...</span></div></div>');

                var button = $(event.relatedTarget);
                var id = button.data('id');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ route('proposals.carregar.whatsapp') }}", type: "POST", data: { id: id }, cache: false, processData: true,
                    success: function(data) {
                        $("#content_modal_load_send_wp").html(data);

                        const choices2 = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione os usuários...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar'  });

                        $('.tags').each(function() {
                            $(this).tagify({ duplicates: false, suggestionsMinChars: 3 });
                        });
                    },
                });
            });
        });
    </script>
@endsection