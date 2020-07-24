@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Configurações</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item d-none"><a class="nav-link" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1">Legenda Mapa Empreendimento</a></li>
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2">Etapas Construção</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab2">Índices de Correção Monetária</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane" id="tab1" role="tabpanel">
                        <div class="card border-top-0">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-12 col-sm-6">
                                        <form action="" method="POST">
                                            {{ csrf_field() }}
                                            <table class="table table-sm table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th>Status</th>
                                                        <th>Cor</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(getProposalStatus() as $status)
                                                        <tr>
                                                            <td>{{ getProposalStatusName($status) }}</td>
                                                            <td><input type="text" class="form-control jscolor" name="status[{{ $status }}]" value="{{ isset($colors[$status]) ? $colors[$status] : '' }}"></td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <button type="submit" class="btn btn-success">Salvar</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane active" id="tab2" role="tabpanel">
                        <div class="card border-top-0">
                            <div class="card-body">
                                <div class="form-group">
                                    <button class="btn btn-info" id="add_stage">Adicionar etapa</button>
                                </div>
                                <div class="row">
                                    <div class="col-12 col-sm-8">
                                        <form action="" method="POST">
                                            {{ csrf_field() }}
                                            <table class="table table-sm table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th width="40%">Etapa</th>
                                                        <th width="40%">Ícone</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="stages">
                                                    @foreach($stages as $key => $stage)
                                                        <tr>
                                                            <td><input type="text" name="old_stages[{{ $stage->id }}]" class="form-control" value="{{ $stage->name }}" required></td>
                                                            <td>
                                                                <select name="old_icons[{{ $stage->id }}]" class="form-control" required>
                                                                    <option value="">Selecione...</option>
                                                                    <option value="fas fa-tractor" {{ $stage->icon == 'fas fa-tractor' ? 'selected' : '' }}>fas fa-tractor</option>
                                                                    <option value="fas fa-water" {{ $stage->icon == 'fas fa-water' ? 'selected' : '' }}>fas fa-water</option>
                                                                    <option value="fas fa-road" {{ $stage->icon == 'fas fa-road' ? 'selected' : '' }}>fas fa-road</option>
                                                                    <option value="fas fa-archway" {{ $stage->icon == 'fas fa-archway' ? 'selected' : '' }}>fas fa-archway</option>
                                                                    <option value="fas fa-door-open" {{ $stage->icon == 'fas fa-door-open' ? 'selected' : '' }}>fas fa-door-open</option>
                                                                    <option value="fas fa-lightbulb" {{ $stage->icon == 'fas fa-lightbulb' ? 'selected' : '' }}>fas fa-lightbulb</option>
                                                                    <option value="fas fa-tree" {{ $stage->icon == 'fas fa-tree' ? 'selected' : '' }}>fas fa-tree</option>
                                                                    <option value="fas fa-file-alt" {{ $stage->icon == 'fas fa-file-alt' ? 'selected' : '' }}>fas fa-file-alt</option>
                                                                    <option value="fas fa-futbol" {{ $stage->icon == 'fas fa-futbol' ? 'selected' : '' }}>fas fa-futbol</option>
                                                                    <option value="fas fa-tint" {{ $stage->icon == 'fas fa-tint' ? 'selected' : '' }}>fas fa-tint</option>
                                                                    <option value="fas fa-toilet-paper" {{ $stage->icon == 'fas fa-toilet-paper' ? 'selected' : '' }}>fas fa-toilet-paper</option>
                                                                    <option value="fas fa-ruler-combined" {{ $stage->icon == 'fas fa-ruler-combined' ? 'selected' : '' }}>fas fa-ruler-combined</option>
                                                                    <option value="fas fa-drafting-compass" {{ $stage->icon == 'fas fa-drafting-compass' ? 'selected' : '' }}>fas fa-drafting-compass</option>
                                                                    <option value="fas fa-paint-roller" {{ $stage->icon == 'fas fa-paint-roller' ? 'selected' : '' }}>fas fa-paint-roller</option>
                                                                    <option value="fas fa-pencil-ruler" {{ $stage->icon == 'fas fa-pencil-ruler' ? 'selected' : '' }}>fas fa-pencil-ruler</option>
                                                                    <option value="fas fa-snowplow" {{ $stage->icon == 'fas fa-snowplow' ? 'selected' : '' }}>fas fa-snowplow</option>
                                                                    <option value="fas fa-tasks" {{ $stage->icon == 'fas fa-tasks' ? 'selected' : '' }}>fas fa-tasks</option>
                                                                </select>
                                                            </td>
                                                            <td></td>
                                                        </tr>
                                                    @endforeach
                                                    <tr>
                                                        <td><input type="text" name="stages[]" class="form-control" required></td>
                                                        <td>
                                                            <select name="icons[]" class="form-control" required>
                                                                <option value="">Selecione...</option>
                                                                <option value="fas fa-tractor">fas fa-tractor</option>
                                                                <option value="fas fa-water">fas fa-water</option>
                                                                <option value="fas fa-road">fas fa-road</option>
                                                                <option value="fas fa-archway">fas fa-archway</option>
                                                                <option value="fas fa-door-open">fas fa-door-open</option>
                                                                <option value="fas fa-lightbulb">fas fa-lightbulb</option>
                                                                <option value="fas fa-tree">fas fa-tree</option>
                                                                <option value="fas fa-file-alt">fas fa-file-alt</option>
                                                                <option value="fas fa-futbol">fas fa-futbol</option>
                                                                <option value="fas fa-tint">fas fa-tint</option>
                                                                <option value="fas fa-toilet-paper">fas fa-toilet-paper</option>
                                                                <option value="fas fa-ruler-combined">fas fa-ruler-combined</option>
                                                                <option value="fas fa-drafting-compass">fas fa-drafting-compass</option>
                                                                <option value="fas fa-paint-roller">fas fa-paint-roller</option>
                                                                <option value="fas fa-pencil-ruler">fas fa-pencil-ruler</option>
                                                                <option value="fas fa-snowplow">fas fa-snowplow</option>
                                                                <option value="fas fa-tasks">fas fa-tasks</option>
                                                            </select>
                                                        </td>
                                                        <td><button type="button" class="btn btn-danger remove_stage">X</button></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <button type="submit" class="btn btn-success">Salvar</button>
                                        </form>
                                    </div>
                                    <div class="col-12 col-sm-4">
                                        <h3>Ícones disponíveis</h3>
                                        <ul class="list-unstyled">
                                            <li><h4>fas fa-tractor - <i class="fas fa-tractor"></i></h4></li>
                                            <li><h4>fas fa-water - <i class="fas fa-water"></i></h4></li>
                                            <li><h4>fas fa-road - <i class="fas fa-road"></i></h4></li>
                                            <li><h4>fas fa-archway - <i class="fas fa-archway"></i></h4></li>
                                            <li><h4>fas fa-door-open - <i class="fas fa-door-open"></i></h4></li>
                                            <li><h4>fas fa-lightbulb - <i class="fas fa-lightbulb"></i></h4></li>
                                            <li><h4>fas fa-tree - <i class="fas fa-tree"></i></h4></li>
                                            <li><h4>fas fa-file-alt - <i class="fas fa-file-alt"></i></h4></li>
                                            <li><h4>fas fa-futbol - <i class="fas fa-futbol"></i></h4></li>
                                            <li><h4>fas fa-tint - <i class="fas fa-tint"></i></h4></li>
                                            <li><h4>fas fa-toilet-paper - <i class="fas fa-toilet-paper"></i></h4></li>
                                            <li><h4>fas fa-ruler-combined - <i class="fas fa-ruler-combined"></i></h4></li>
                                            <li><h4>fas fa-drafting-compass - <i class="fas fa-drafting-compass"></i></h4></li>
                                            <li><h4>fas fa-paint-roller - <i class="fas fa-paint-roller"></i></h4></li>
                                            <li><h4>fas fa-pencil-ruler - <i class="fas fa-pencil-ruler"></i></h4></li>
                                            <li><h4>fas fa-snowplow - <i class="fas fa-snowplow"></i></h4></li>
                                            <li><h4>fas fa-tasks - <i class="fas fa-tasks"></i></h4></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab3" role="tabpanel">
                        <div class="card border-top-0">
                            <div class="card-body">
                                <div class="card mb-4">
                                    <div class="card-header">Criar índice</div>
                                    <div class="card-body">
                                        <form class="form-inline" action="" method="POST">
                                            {{ csrf_field() }}
                                            <input type="text" class="form-control mb-2 mr-sm-2 w-50" name="index" placeholder="Ex: Nome do índice" required>
                                            <button type="submit" class="btn btn-primary mb-2">Criar</button>
                                        </form>
                                    </div>
                                </div>

                                @if($indexes->count())
                                    <div class="accordion" id="indexes">
                                        @foreach($indexes as $key => $index)
                                            <div class="card border-bottom">
                                                <div class="card-header" data-toggle="collapse" data-target="#collapse{{ $key }}">{{ $index->name }}</div>
                                                <div id="collapse{{ $key }}" class="collapse" data-parent="#indexes">
                                                    <form action="" method="POST">
                                                        {{ csrf_field() }}
                                                        <input type="hidden" name="index_id" value="{{ $index->id }}">
                                                        <div class="card-body">
                                                            <table class="table table-sm table-bordered text-center">
                                                                <thead>
                                                                    <tr>
                                                                        <th></th>
                                                                        @foreach(getMonthsAbbr() as $month)
                                                                            <th>{{ $month }}</th>
                                                                        @endforeach
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    @foreach($index->history->groupBy('year') as $key => $history)
                                                                        <tr>
                                                                            <td>{{ $key }}</td>
                                                                            @foreach(getMonthsAbbr() as $ind => $month)
                                                                                <?php $value = $history->where('month', $ind)->first()->value; ?>
                                                                                <td>
                                                                                    @if($value)
                                                                                        {{ formatMonetaryCorrectionIndex($value) }}
                                                                                    @else
                                                                                        @if(\Carbon\Carbon::now()->endOfMonth() >= \Carbon\Carbon::createFromDate($key, $ind, 1))
                                                                                            <input type="text" class="form-control form-control-sm index" name="history[{{ $key }}-{{ $ind }}]">
                                                                                        @endif
                                                                                    @endif
                                                                                </td>
                                                                            @endforeach
                                                                        </tr>
                                                                    @endforeach
                                                                </tbody>
                                                            </table>
                                                            <div class="d-flex justify-content-end">
                                                                <button type="submit" class="btn btn-success">Salvar informações desse índice</button>
                                                            </div>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/jscolor.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('.index').maskMoney({ thousands: '.', decimal: ',', allowZero: false, precision: 4, allowNegative: true, allowEmpty: true });

            $(document).on('click', '#add_stage', function(){
                var clone = $("#stages").find('tr').last().clone();
                $("#stages").append(clone);
            });

            $(document).on('click', '.remove_stage', function(){
                $(this).closest('tr').remove();
            });

        })
    </script>
@endsection