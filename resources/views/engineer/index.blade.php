@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div id="accordion">
                    @foreach($projects as $project)
                        <div class="card mb-1">
                            <div class="card-header d-flex pointer" id="heading{{ $project->id }}" data-toggle="collapse" data-target="#collapse{{ $project->id }}">{{ $project->name }}</div>
                            <div id="collapse{{ $project->id }}" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    @if(Auth::user()->role == 'ADMIN' || Auth::user()->checkPermission($project->id, ['UPDATE_CONSTRUCTION']))
                                        <form action="{{ action('EngineerController@stage') }}" method="POST">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="project" value="{{ $project->id }}">
                                            <div class="row justify-content-between">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <select name="position" class="form-control" required>
                                                            <option value="">Selecione o posicionamento...</option>
                                                            <option {{ $project->map_stages_position == 'TOP_LEFT' ? 'selected' : '' }} value="TOP_LEFT">Topo - Esquerda</option>
                                                            <option {{ $project->map_stages_position == 'TOP_CENTER' ? 'selected' : '' }} value="TOP_CENTER">Topo - Centro</option>
                                                            <option {{ $project->map_stages_position == 'TOP_RIGHT' ? 'selected' : '' }} value="TOP_RIGHT">Topo - Direita</option>
                                                            <option {{ $project->map_stages_position == 'CENTER_LEFT' ? 'selected' : '' }} value="CENTER_LEFT">Centro - Esquerda</option>
                                                            <option {{ $project->map_stages_position == 'CENTER_CENTER' ? 'selected' : '' }} value="CENTER_CENTER">Centro - Centro</option>
                                                            <option {{ $project->map_stages_position == 'CENTER_RIGHT' ? 'selected' : '' }} value="CENTER_RIGHT">Centro - Direita</option>
                                                            <option {{ $project->map_stages_position == 'BOTTOM_LEFT' ? 'selected' : '' }} value="BOTTOM_LEFT">Baixo - Esquerda</option>
                                                            <option {{ $project->map_stages_position == 'BOTTOM_CENTER' ? 'selected' : '' }} value="BOTTOM_CENTER">Baixo - Centro</option>
                                                            <option {{ $project->map_stages_position == 'BOTTOM_RIGHT' ? 'selected' : '' }} value="BOTTOM_RIGHT">Baixo - Direita</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-info btn-block add_stage" data-project="{{ $project->id }}">Adicionar etapa</button>
                                                    </div>
                                                </div>
                                            </div>
                                            <table class="table table-sm table-bordered text-center">
                                                <thead>
                                                    <tr>
                                                        <th width="20%">Etapa</th>
                                                        <th width="15%">Porcentagem</th>
                                                        <th width="15%">Exibir</th>
                                                        <th width="25%">Início</th>
                                                        <th width="15%">Exibir início</th>
                                                        <th></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="stages{{ $project->id }}">
                                                    @foreach($project->stages as $st)
                                                        <tr>
                                                            <td>
                                                                <select name="old_stage[{{ $st->id }}]" class="form-control" required>
                                                                    @foreach($stages as $stage)
                                                                        <option value="{{ $stage->id }}" {{ $st->stage_id == $stage->id ? 'selected' : '' }}>{{ $stage->name }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <input type="number" name="old_percentage[{{ $st->id }}]" class="form-control" min="0" max="100" value="{{ $st->percentage }}" required>
                                                            </td>
                                                            <td>
                                                                <select name="old_visible[{{ $st->id }}]" class="form-control" required>
                                                                    <option value="1" {{ $st->is_visible == 1 ? 'selected' : '' }}>Sim</option>
                                                                    <option value="0" {{ $st->is_visible == 0 ? 'selected' : '' }}>Não</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                    $m = $y = null;
                                                                    if($st->start_at) $m = \Carbon\Carbon::parse($st->start_at)->month;
                                                                    if($st->start_at) $y = \Carbon\Carbon::parse($st->start_at)->year;
                                                                ?>
                                                                <div class="input-group">
                                                                    <select name="old_month[{{ $st->id }}]" class="form-control">
                                                                        <option value="">Selecione...</option>
                                                                        @foreach(getMonths() as $key => $month)
                                                                            <option value="{{ $key }}" {{ $m && $m == $key ? 'selected' : '' }}>{{ $month }}</option>
                                                                        @endforeach
                                                                    </select>
                                                                    <div class="input-group-prepend"><span class="input-group-text">/</span></div>
                                                                    <select name="old_year[{{ $st->id }}]" class="form-control">
                                                                        <option value="">Selecione...</option>
                                                                        @for($i = date('Y'); $i < date('Y')+30; $i++)
                                                                            <option value="{{ $i }}" {{ $y && $y == $i ? 'selected' : '' }}>{{ $i }}</option>
                                                                        @endfor
                                                                    </select>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <select name="old_show_start[{{ $st->id }}]" class="form-control" required>
                                                                    <option value="1" {{ $st->show_start_at == 1 ? 'selected' : '' }}>Sim</option>
                                                                    <option value="0" {{ $st->show_start_at == 0 ? 'selected' : '' }}>Não</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-danger remove_stage">X</button>
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                            <div class="row justify-content-between">
                                                <div class="col-12 col-sm-4">
                                                    <button type="submit" class="btn btn-success btn-block">Salvar andamento da obra</button>
                                                </div>
                                            </div>
                                        </form>
                                    @else
                                        <h5 class="text-center m-0">Você não tem permissão necessária para esse módulo.</h5>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <table class="table table-sm table-bordered text-center d-none" id="aux_table">
        <tbody>
            <tr>
                <td>
                    <select name="stage[]" class="form-control" required>
                        @foreach($stages as $stage)
                            <option value="{{ $stage->id }}">{{ $stage->name }}</option>
                        @endforeach
                    </select>
                </td>
                <td>
                    <input type="number" name="percentage[]" class="form-control" min="0" max="100" required>
                </td>
                <td>
                    <select name="visible[]" class="form-control" required>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </td>
                <td>
                    <div class="input-group">
                        <select name="month[]" class="form-control">
                            <option value="">Selecione...</option>
                            @foreach(getMonths() as $key => $month)
                                <option value="{{ $key }}">{{ $month }}</option>
                            @endforeach
                        </select>
                        <div class="input-group-prepend"><span class="input-group-text">/</span></div>
                        <select name="year[]" class="form-control">
                            <option value="">Selecione...</option>
                            @for($i = date('Y'); $i < date('Y')+30; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                </td>
                <td>
                    <select name="show_start[]" class="form-control" required>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </td>
                <td>
                    <button type="button" class="btn btn-danger remove_stage">X</button>
                </td>
            </tr>
        </tbody>
    </table>

@endsection
@section('js')
    <script>
        $(document).ready(function(){
            $("#form_block").validate({
                rules: {
                    label: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });

            $(document).on('click', '.add_stage', function() {
                var id = $(this).data('project')
                var clone = $("#aux_table tbody").find('tr').first().clone();
                $("#stages"+id).append(clone);
            });

            $(document).on('click', '.remove_stage', function(){
                $(this).closest('tr').remove();
            });

        });
    </script>
@endsection