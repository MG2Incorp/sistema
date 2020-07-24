@extends('layouts.app')
@section('css')
    <link rel="stylesheet" type="text/css" href="{{ asset('css/choices.css') }}">
    <style>
        .badge-green { background-color: #8BC34A }
        .badge-orange { background-color: #FF9800 }
        .badge-red { background-color: #F44336 }
        .badge-blue { background-color: #2196F3 }

        .status-yellow { color: #FFEB3B; }
        .status-blue { color: #2196F3; }
        .status-grey { color: #757575; }
        .status-orange { color: #FF9800; }
        .status-purple { color: #9C27B0; }
        .status-red { color: #F44336; }
        .status-black { color: #000000; }
    </style>
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12 col-sm-2 d-flex flex-column justify-content-end mr-auto">
                                <div class="dropdown">
                                    <button class="btn btn-secondary dropdown-toggle btn-block" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Tabela de Preços</button>
                                    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item" href="{{ route('map.export') }}?project={{ $project->id }}&type=SHEET">Exportar em XLSX</a>
                                        <a class="dropdown-item" href="{{ route('map.export') }}?project={{ $project->id }}&type=PDF">Exportar em PDF</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-2">
                                <form method="GET" action="" id="form_project">
                                    <input type="hidden" name="empreendimento" value="{{ $project->id }}">
                                    <label>Você está no bloco</label>
                                    <select name="predio" class="form-control" id="select_project">
                                        @foreach($project->buildings as $predio)
                                            <option value="{{ $predio->id }}" {{ $predio->id == $building->id ? 'selected' : '' }}>{{ $predio->name }}</option>
                                        @endforeach
                                    </select>
                                </form>
                            </div>
                            <div class="col-12 col-sm-4">
                                @if(Auth::user()->checkPermission($project->id, ['BLOCK_CREATE']) || Auth::user()->role == 'ADMIN')
                                    <form class="" method="POST" action="" id="form_block">
                                        @csrf
                                        <div class="form-group m-0">
                                            <label>Adicionar quadra/andar</label>
                                            <div class="input-group">
                                                <input type="hidden" name="empreendimento" value="{{ $project->id }}">
                                                <input type="hidden" name="insert_block" value="1">
                                                <input type="text" class="form-control" placeholder="Identificação" name="label" required>
                                                <div class="input-group-append"><button class="btn btn-outline-secondary" type="submit"><i class="fas fa-plus"></i></button></div>
                                            </div>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <div class="col-3"><h4 class="m-0 p-0"><span class="badge text-white badge-green p-3 w-100">Disponível ({{ $project->getPropertiesAvailable() }})</span></h4></div>
            <div class="col-3"><h4 class="m-0 p-0"><span class="badge text-white badge-orange p-3 w-100">Análise ({{ $project->proposals_review->groupBy('property_id')->count() }})</span></h4></div>
            <div class="col-3"><h4 class="m-0 p-0"><span class="badge text-white badge-red p-3 w-100">Vendido ({{ $project->proposals_sold->count() }})</span></h4></div>
            <div class="col-3"><h4 class="m-0 p-0"><span class="badge text-white badge-blue p-3 w-100">Bloqueado ({{ $project->properties->where('situation', 'BLOCKED')->count() }})</span></h4></div>
        </div>
        <div class="row">
            <div class="col-12">
                <div id="accordion">
                    @foreach($blocks->sortBy('label') as $key => $block)
                        <div class="card mb-1">
                            <div class="card-header d-flex pointer" id="heading{{ $block->id }}" data-toggle="collapse" data-target="#collapse{{ $block->id }}" aria-expanded="true" aria-controls="collapse{{ $block->id }}">
                                {{ $block->building->project->type }} {{ $block->label }}
                            </div>
                            <div id="collapse{{ $block->id }}" class="collapse" aria-labelledby="heading{{ $block->id }}" data-parent="#accordion">
                                <div class="card-body">
                                    <form action="" method="POST">
                                        @csrf
                                        @if(Auth::user()->checkPermission($block->building->project->id, ['PROPERTY_CREATE']))
                                            <button type="button" class="btn btn-success" data-toggle="modal" data-target="#add_property{{ $block->id }}">Adicionar imóvel</button>
                                        @endif
                                        @if(Auth::user()->checkPermission($block->building->project->id, ['BLOCK_DELETE']))
                                            <input type="hidden" name="block_id" value="{{ $block->id }}">
                                            <input type="hidden" name="block_delete" value="1">
                                            <button type="submit" class="btn btn-danger float-right">Remover quadra</button>
                                        @endif
                                    </form>
                                    <hr>
                                    <div class="row">
                                        @foreach($block->properties->sortBy('number', SORT_NUMERIC, false) as $property)
                                            @if($property->proposals_actives->count() > 0)
                                                @switch($property->proposals_actives->first()->status)
                                                    @case('RESERVED') @case('QUEUE_1') @case('QUEUE_2') @php $color = 'status-yellow'; @endphp @break
                                                    @case('DOCUMENTS_PENDING') @php $color = 'status-blue'; @endphp @break
                                                    @case('PROPOSAL') @case('PROPOSAL_REVIEW') @case('DOCUMENTS_REVIEW') @php $color = 'status-grey'; @endphp @break
                                                    @case('CONTRACT_ISSUE') @case('CONTRACT_AVAILABLE') @php $color = 'status-orange'; @endphp @break
                                                    @case('PENDING_SIGNATURE_CLIENT') @case('PENDING_SIGNATURE_CONSTRUCTOR') @php $color = 'status-purple'; @endphp @break
                                                    @case('SOLD') @php $color = 'status-red'; @endphp @break
                                                    @case('REFUSED') @case('CANCELED') @php $color = 'status-black'; @endphp @break
                                                @endswitch
                                            @else
                                                @php $color = 'text-secondary'; @endphp
                                            @endif
                                            <div class="col-6 col-sm-2 mb-1">
                                                <div class="card border-{{ $color }} h-100">
                                                    <div class="card-header bg-{{ $color }} text-center py-1">{{ $property->number }}</div>
                                                    @if($property->situation == 'AVAILABLE')
                                                        <div class="card-body" align="center">
                                                            <h6 class="card-subtitle mb-2 text-muted text-center">R$ {{ formatMoney($property->value) }}</h6>
                                                            <h6 class="card-subtitle mb-2 text-muted text-center">{{ $property->size }} m²</h6>
                                                            <h6 class="card-subtitle mb-2 text-muted text-center">{{ $property->notes }}</h6>
                                                            @if($property->proposals_actives->count() == 0)
                                                                <h5 class="card-title text-success">Disponível</h5>
                                                            @else
                                                                <h5 class="card-title {{ $color }}">{{ getProposalStatusName($property->proposals_actives->first()->status) }}</h5>
                                                            @endif
                                                        </div>
                                                        @if(!$property->proposal_sold)
                                                            <div class="card-footer" align="center">
                                                                <div class="btn-group dropright">
                                                                    <button type="button" class="btn btn-secondary btn-xs" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                                    <div class="dropdown-menu dropdown-menu-right">
                                                                        @if(Auth::user()->checkPermission($project->id, ['PROPOSAL_CREATE']))
                                                                            @if($property->proposals_actives->count() < 3 && !$property->proposal_sold)
                                                                                <a href="{{ route('proposals.create') }}?imovel={{ $property->id }}" class="dropdown-item">Pedir reserva</a>
                                                                            @endif
                                                                        @endif
                                                                        @if(Auth::user()->checkPermission($project->id, ['PROPERTY_EDIT']))
                                                                            <button class="dropdown-item pointer" data-toggle="modal" data-target="#edit_property{{ $property->id }}">Editar</button>
                                                                        @endif
                                                                        @if(Auth::user()->checkPermission($project->id, ['PROPERTY_STATUS']))
                                                                            @if($property->proposals_actives->count() == 0)
                                                                                <form action="" method="POST">
                                                                                    @csrf
                                                                                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                                                                                    <input type="hidden" name="block" value="1">
                                                                                    <button type="submit" class="dropdown-item pointer">Bloquear</button>
                                                                                </form>
                                                                            @endif
                                                                        @endif
                                                                        @if(Auth::user()->checkPermission($project->id, ['PROPERTY_DELETE']))
                                                                            <form action="" method="POST">
                                                                                @csrf
                                                                                <input type="hidden" name="property_id" value="{{ $property->id }}">
                                                                                <input type="hidden" name="delete" value="1">
                                                                                <button type="submit" class="dropdown-item pointer">Deletar</button>
                                                                            </form>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @else
                                                        <div class="card-body" align="center">
                                                            <h5>Imóvel bloqueado no momento.</h5>
                                                        </div>
                                                        @if(Auth::user()->checkPermission($project->id, ['PROPERTY_STATUS']))
                                                            <div class="card-footer" align="center">
                                                                <form action="" method="POST">
                                                                    @csrf
                                                                    <input type="hidden" name="property_id" value="{{ $property->id }}">
                                                                    <input type="hidden" name="allow" value="1">
                                                                    <button type="submit" class="btn btn-success btn-xs">Desbloquear</button>
                                                                </form>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </div>
                                            </div>
                                            @if(Auth::user()->checkPermission($project->id, ['PROPERTY_EDIT']))
                                                <div class="modal fade" id="edit_property{{ $property->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                                    <div class="modal-dialog modal-dialog-centered" role="document">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title" id="exampleModalLabel">{{ $block->building->project->type }} {{ $block->label }} - Editar imóvel</h5>
                                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <form action="{{ action('PropertyController@update', $property->id) }}" method="POST" id="form_property">
                                                                    @csrf
                                                                    <div class="row">
                                                                        <div class="col-12 col-sm-3">
                                                                            <div class="form-group">
                                                                                <label>Número</label>
                                                                                <input type="text" name="number" class="form-control" value="{{ $property->number }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-5">
                                                                            <div class="form-group">
                                                                                <label>Valor</label>
                                                                                <div class="input-group">
                                                                                    <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                                                                    <input type="text" name="value" class="form-control money" value="{{ formatMoney($property->value) }}">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-4">
                                                                            <div class="form-group">
                                                                                <label>Área</label>
                                                                                <div class="input-group">
                                                                                    <input type="text" name="size" class="form-control money" value="{{ formatMoney($property->size) }}">
                                                                                    <div class="input-group-append"><span class="input-group-text">m²</span></div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="row">
                                                                        <div class="col-12 col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Número da Matrícula</label>
                                                                                <input type="text" name="numero_matricula" class="form-control" value="{{ $property->numero_matricula }}">
                                                                            </div>
                                                                        </div>
                                                                        <div class="col-12 col-sm-6">
                                                                            <div class="form-group">
                                                                                <label>Cadastro Imobiliário</label>
                                                                                <input type="text" name="cadastro_imobiliario" class="form-control" value="{{ $property->cadastro_imobiliario }}">
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <!-- <div class="form-group">
                                                                        <label>Proprietário</label>
                                                                        <input type="text" name="owner" class="form-control" value="{{ $property->owner }}">
                                                                    </div> -->
                                                                    @if($accounts->count())
                                                                        <div class="form-group">
                                                                            <label>Proprietário/Conta</label>
                                                                            <select class="form-control js-choice" name="account_id" placeholder="Selecione...">
                                                                                <option value="" placeholder>Selecione...</option>
                                                                                @foreach($accounts as $account)
                                                                                    <option value="{{ $account->id }}" {{ $account->id == $property->account_id ? 'selected' : '' }}>{{ $account->owner->alias }} | {{ getBankCode($account->bank_code) }} | Ag. {{ $account->agency.'-'.$account->agency_dv }} | Num. {{ $account->number.'-'.$account->number_dv }}</option>
                                                                                @endforeach
                                                                            </select>
                                                                        </div>
                                                                    @endif
                                                                    <div class="form-group">
                                                                        <label>Dimensões</label>
                                                                        <textarea class="form-control" name="dimensions" rows="3" style="resize: none">{{ $property->dimensions }}</textarea>
                                                                    </div>
                                                                    <div class="form-group">
                                                                        <label>Observações</label>
                                                                        <textarea class="form-control" name="notes" rows="3" style="resize: none">{{ $property->notes }}</textarea>
                                                                    </div>
                                                                    <center><button type="submit" class="btn btn-success">Editar imóvel</button></center>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if(Auth::user()->checkPermission($block->building->project->id, ['PROPERTY_CREATE']))
                            <div class="modal fade" id="add_property{{ $block->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h5 class="modal-title" id="exampleModalLabel">{{ $block->building->project->type }} {{ $block->label }} - Adicionar imóvel</h5>
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <form action="{{ action('PropertyController@store') }}" method="POST" id="form_property">
                                                @csrf
                                                <input type="hidden" name="block_id" value="{{ $block->id }}">
                                                <div class="row">
                                                    <div class="col-12 col-sm-3">
                                                        <div class="form-group">
                                                            <label>Número</label>
                                                            <input type="text" name="number" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-5">
                                                        <div class="form-group">
                                                            <label>Valor</label>
                                                            <div class="input-group">
                                                                <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                                                <input type="text" name="value" class="form-control money" value="0,00">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-4">
                                                        <div class="form-group">
                                                            <label>Área</label>
                                                            <div class="input-group">
                                                                <input type="text" name="size" class="form-control money" value="0,00">
                                                                <div class="input-group-append"><span class="input-group-text">m²</span></div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label>Número da Matrícula</label>
                                                            <input type="text" name="numero_matricula" class="form-control">
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label>Cadastro Imobiliário</label>
                                                            <input type="text" name="cadastro_imobiliario" class="form-control">
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- <div class="form-group">
                                                    <label>Proprietário</label>
                                                    <input type="text" name="owner" class="form-control">
                                                </div> -->
                                                @if($accounts->count())
                                                    <div class="form-group">
                                                        <label>Proprietário/Conta</label>
                                                        <select class="form-control js-choice" name="account_id" placeholder="Selecione...">
                                                            <option value="" placeholder>Selecione...</option>
                                                            @foreach($accounts as $account)
                                                                <option value="{{ $account->id }}">{{ $account->owner->alias }} | {{ getBankCode($account->bank_code) }} | Ag. {{ $account->agency.'-'.$account->agency_dv }} | Num. {{ $account->number.'-'.$account->number_dv }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                @endif
                                                <div class="form-group">
                                                    <label>Dimensões</label>
                                                    <textarea class="form-control" name="dimensions" rows="3" style="resize: none"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <label>Observações</label>
                                                    <textarea class="form-control" name="notes" rows="3" style="resize: none"></textarea>
                                                </div>
                                                <center><button type="submit" class="btn btn-success">Adicionar imóvel</button></center>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('js/choices.js') }}"></script>
    <script>
        $(document).ready(function() {
            const choices = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholder: true, placeholderValue: 'Selecione...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: '' });

            $(document).on('change', '#select_project', function(){
                $("#form_project").submit();
            });

            $("#form_property").validate({
                rules: {
                    number: { required: true, normalizer: function(value) { return $.trim(value); } },
                    value: { required: true, normalizer: function(value) { return $.trim(value); } },
                    size: { required: true, normalizer: function(value) { return $.trim(value); } },
                    notes: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });

            $("#form_block").validate({
                rules: {
                    label: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection