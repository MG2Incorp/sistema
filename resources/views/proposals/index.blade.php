@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card mb-1">
                    <div class="card-body">
                        <form class="form-inline" method="GET" action="">
                            <input type="hidden" name="listar" value="true">
                            <label class="mr-1">Empreendimento</label>
                            <select name="empreendimento" id="project" class="form-control mr-3" required>
                                <option value="">Selecione...</option>
                                @foreach($projects->sortBy('name') as $project)
                                    <option value="{{ $project->id }}" {{ isset($empreendimento) && $empreendimento->id == $project->id ? 'selected' : '' }}>{{ $project->name }}</option>
                                @endforeach
                            </select>
                            <label class="mr-1">Bloco</label>
                            @if(isset($bloco))
                                <select name="bloco" id="building" class="form-control mr-3" required>
                                    <option value="">Selecione...</option>
                                    @foreach($empreendimento->buildings->sortBy('name') as $building)
                                        <option value="{{ $building->id }}" {{ $bloco->id == $building->id ? 'selected' : '' }}>{{ $building->name }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select name="bloco" id="building" class="form-control mr-3" required>
                                    <option value="">Selecione um projeto.</option>
                                </select>
                            @endif
                            <label class="mr-1">Andar</label>
                            @if(isset($andar))
                                <select name="andar" id="block" class="form-control mr-3" required>
                                    <option value="">Selecione...</option>
                                    @foreach($bloco->blocks->sortBy('label') as $block)
                                        <option value="{{ $block->id }}" {{ $block->id == $andar->id ? 'selected' : '' }}>{{ $block->label }}</option>
                                    @endforeach
                                </select>
                            @else
                                <select name="andar" id="block" class="form-control mr-3" required>
                                    <option value="">Selecione um bloco.</option>
                                </select>
                            @endif
                            <button type="submit" class="btn btn-primary">Listar</button>
                        </form>
                    </div>
                </div>

                @forelse($properties as $property)
                    <div class="card mb-1">
                        <div class="card-body">
                            <h5 class="card-title">Número: #{{ $property->id }}</h5>
                            <table class="table table-sm table-bordered m-0" style="font-size: 14px">
                                <thead>
                                    <tr class="text-center">
                                        <th width="5%">Cód.</th>
                                        <th width="10%">Data</th>
                                        <th>Proponente</th>
                                        <th>Corretor</th>
                                        <th width="11%">Proposta</th>
                                        <th width="11%">Unidade</th>
                                        <th>Status</th>
                                        <th width="8%"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($property->proposals_actives as $key => $proposal)
                                        <tr class="text-center">
                                            <td width="30%">{{ $proposal->id }}</td>
                                            <td>{{ dateString($proposal->created_at) }}</td>
                                            <td>{{ $proposal->main_proponent->name }}</td>
                                            <td>{{ $proposal->user->name }} ({{ $proposal->user->company->name }})</td>
                                            <td>R$ {{ formatMoney($proposal->payments->sum('total_value')) }}</td>
                                            <td>R$ {{ formatMoney($proposal->property->value) }}</td>
                                            <td>
                                                @switch($proposal->status)
                                                    @case('RESERVED') @case('QUEUE_1') @case('QUEUE_2') @php $color = 'warning'; @endphp @break
                                                    @case('PROPOSAL') @case('PROPOSAL_REVIEW') @case('DOCUMENTS_REVIEW') @php $color = 'secondary'; @endphp @break
                                                    @case('CONTRACT_ISSUE') @php $color = 'info'; @endphp @break
                                                    @case('CONTRACT_AVAILABLE') @php $color = 'primary'; @endphp @break
                                                    @case('IN_SIGNATURE') @case('SOLD') @php $color = 'success'; @endphp @break
                                                    @case('REFUSED') @case('CANCELED') @php $color = 'danger'; @endphp @break
                                                @endswitch
                                                <h5 class="m-0 p-0"><span class="badge badge-{{ $color }}">{{ getProposalStatusName($proposal->status) }}</span></h5>
                                            </td>
                                            <td width="8%">
                                                <div class="btn-group">
                                                    <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                    <div class="dropdown-menu dropdown-menu-right">
                                                        <a class="dropdown-item" href="{{ route('proposals.show') }}?proposta={{ $proposal->id }}" target="_BLANK">Visualizar proposta</a>
                                                        <a class="dropdown-item" href="{{ route('proposals.print') }}?proposta={{ $proposal->id }}" target="_BLANK">Emitir proposta</a>
                                                        @if(Auth::user()->checkPermission($project->id, ['PROPOSAL_EDIT']))
                                                            <a class="dropdown-item" href="{{ route('proposals.edit', $proposal->id) }}">Editar proposta</a>
                                                        @endif
                                                        <button class="dropdown-item" type="button" data-toggle="modal" data-target="#docs{{ $proposal->id }}">Documentação</button>
                                                        @if(!in_array($proposal->status, ['QUEUE_1', 'QUEUE_2']))
                                                            <div class="dropdown-divider"></div>
                                                            <h6 class="dropdown-header">Alterar status</h6>
                                                            <form class="px-4 pb-2" method="GET" action="{{ action('ProposalController@status') }}">
                                                                <div class="form-group">
                                                                    <input type="hidden" name="proposta" value="{{ $proposal->id }}">
                                                                    <select name="status" class="form-control selectpicker">
                                                                        @foreach(getProposalStatus() as $status)
                                                                            @if($status == 'SOLD')
                                                                                @if(Auth::user()->checkPermission($project->id, ['PROPERTY_SOLD']))
                                                                                    <option value="{{ $status }}" {{ $proposal->status == getProposalStatusName($status) ? 'disabled' : '' }}>{{ getProposalStatusName($status) }}</option>
                                                                                @endif
                                                                            @elseif($status == 'CANCELED')
                                                                                @if(Auth::user()->checkPermission($project->id, ['PROPOSAL_DELETE']))
                                                                                    <option value="{{ $status }}" {{ $proposal->status == getProposalStatusName($status) ? 'disabled' : '' }}>{{ getProposalStatusName($status) }}</option>
                                                                                @endif
                                                                            @else
                                                                                <option value="{{ $status }}" {{ $proposal->status == getProposalStatusName($status) ? 'disabled' : '' }}>{{ getProposalStatusName($status) }}</option>
                                                                            @endif
                                                                        @endforeach
                                                                    </select>
                                                                </div>
                                                                <button type="submit" class="btn btn-success btn-sm btn-block">Alterar</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @empty

                @endforelse
            </div>
        </div>
    </div>

    @foreach($proposals as $proposal)
        <div class="modal fade" id="docs{{ $proposal->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Documentação - Proposta #{{ $proposal->id }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <div class="card mb-4">
                            <div class="card-header">Adicionar documento</div>
                            <div class="card-body">
                                <form class="row" action="{{ action('ProposalController@document') }}" method="POST" enctype="multipart/form-data">
                                    @csrf
                                    <input type="hidden" name="proposal_id" value="{{ $proposal->id }}">
                                    <div class="col-12 col-sm-5">
                                        <select name="type" class="form-control" required>
                                            <option value="">Selecione o tipo do arquivo...</option>
                                            <option value="RG">RG</option>
                                            <option value="CPF">CPF</option>
                                            <option value="Comprovante de Residência">Comprovante de Residência</option>
                                            <option value="Comprovante de Renda">Comprovante de Renda</option>
                                            <option value="Certidão de Nascimento">Certidão de Nascimento</option>
                                            <option value="Certidão de Casamento">Certidão de Casamento</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                    <div class="col-12 col-sm-5">
                                        <div class="custom-file">
                                            <input type="file" class="custom-file-input" name="file" required>
                                            <label class="custom-file-label">Selecionar arquivo</label>
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-2">
                                        <button type="submit" class="btn btn-primary">Adicionar</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card">
                            <div class="card-header">Documentos adicionados</div>
                            <div class="card-body">
                                @if($proposal->documents->count())
                                    <table class="table table-hover table-sm table-bordered m-0">
                                        <thead>
                                            <tr class="text-center">
                                                <th width="20%">Data de Envio</th>
                                                <th width="30%">Tipo</th>
                                                <th>Arquivo</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($proposal->documents as $document)
                                                <tr class="text-center">
                                                    <td>{{ dateString($document->created_at) }}</td>
                                                    <td>{{ $document->type }}</td>
                                                    <td>
                                                        <a href="{{ route('proposals.download', $document->file) }}" target="_BLANK">{{ $document->file }}</a>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <h6 class="text-center">Nenhum documento encontrado.</h6>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
@section('js')
    <script>
        $(document).ready(function(){
            $(document).on('change', '#project', function(){
                $("#building").html('<option value="">Carregando...</option>');
                $("#block").html('<option value="">Carregando...</option>');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ URL::route('projects.buildings') }}", type: "POST", data: { id: $(this).val() }, cache: false, processData:true,
                    success:function(data){
                        if(data.length) {
                            $("#building").html(data);
                        } else {
                            $("#building").html('<option value="">Selecione um projeto.</option>');
                            $("#block").html('<option value="">Selecione um bloco.</option>');
                        }
                    }
                });
            });

            $(document).on('change', '#building', function(){
                $("#block").html('<option value="">Carregando...</option>');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ URL::route('projects.blocks') }}", type: "POST", data: { id: $(this).val() }, cache: false, processData:true,
                    success:function(data){
                        if(data.length) {
                            $("#block").html(data);
                        } else {
                            $("#block").html('<option value="">Selecione um bloco.</option>');
                        }
                    }
                });
            });

            $('.dropdown-menu').on('click', function(event) { event.stopPropagation(); });
        });
    </script>
@endsection