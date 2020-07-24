@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.css">
    @switch($project->map_stages_position)
        @case('TOP_LEFT')       <style> .position { z-index: 10 !important; position: fixed; top: 5%; left: 5%; transform: translate(-15%, -5%); } </style>     @break
        @case('TOP_CENTER')     <style> .position { z-index: 10 !important; position: fixed; top: 5%; left: 50%; transform: translate(-50%, -5%); } </style>    @break
        @case('TOP_RIGHT')      <style> .position { z-index: 10 !important; position: fixed; top: 5%; left: 95%; transform: translate(-85%, -5%); } </style>    @break
        @case('CENTER_LEFT')    <style> .position { z-index: 10 !important; position: fixed; top: 50%; left: 5%; transform: translate(-15%, -50%); } </style>   @break
        @case('CENTER_CENTER')  <style> .position { z-index: 10 !important; position: fixed; top: 50%; left: 50%; transform: translate(-50%, -50%); } </style>  @break
        @case('CENTER_RIGHT')   <style> .position { z-index: 10 !important; position: fixed; top: 50%; left: 95%; transform: translate(-85%, -50%); } </style>  @break
        @case('BOTTOM_LEFT')    <style> .position { z-index: 10 !important; position: fixed; top: 95%; left: 5%; transform: translate(-15%, -95%); } </style>   @break
        @case('BOTTOM_CENTER')  <style> .position { z-index: 10 !important; position: fixed; top: 95%; left: 50%; transform: translate(-50%, -95%); } </style>  @break
        @case('BOTTOM_RIGHT')   <style> .position { z-index: 10 !important; position: fixed; top: 95%; left: 95%; transform: translate(-85%, -95%); } </style>  @break
        @default                <style> .position { z-index: 10 !important; position: fixed; top: 5%; left: 5%; transform: translate(-15%, -5%); } </style>     @break
    @endswitch
    <style>
        small, .small { font-size: 10px !important; line-height: 10px !important }
    </style>
@endsection
@section('content')
    @if($project->stages && $project->stages->count())
        <div class="card position p-1" style="width: 200px">
            <div class="card-header p-1 small">Andamento da Obra</div>
            <ul class="list-group list-group-flush">
                @foreach($project->stages->where('is_visible', 1) as $stage)
                    <li class="list-group-item p-0 border-0">
                        <small class="font-weight-bold"><i class="{{ $stage->stage->icon }}"></i> {{ $stage->stage->name }}</small>
                        @if($stage->start_at && $stage->show_start_at)
                            <small class="text-muted">Ínicio em {{ formatDataWithoutDay($stage->start_at) }}</small>
                        @endif
                        <div class="progress" style="height: 8px">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-success" role="progressbar" style="width: {{ $stage->percentage }}%"><b>{{ $stage->percentage }}%</b></div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <img src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->map) }}" id="mapa" usemap="#map{{ $project->id }}" class="map">
	<map name="map{{ $project->id }}">
        @foreach($project->maps as $map)
            @if($map->property && $map->shape && $map->coordinates)
                <?php
                    $html = "<div class='p-2'>";
                    if($project->fields) {
                        foreach (json_decode($project->fields) as $key => $field) {
                            switch ($field) {
                                case 'bloco': $html .= '<div>Bloco: <b>'.$map->property->block->building->name.'</b></div>'; break;
                                case 'quadra': $html .= '<div>Quadra: <b>'.$map->property->block->label.'</b></div>'; break;
                                case 'number': $html .= '<div>Número: <b>'.$map->property->number.'</b></div>'; break;
                                case 'value': $html .= '<div>Valor: <b>R$ '.formatMoney($map->property->value).'</b></div>'; break;
                                case 'notes': $html .= '<div>Obervações: <b>'.$map->property->notes.'</b></div>'; break;
                                case 'size': $html .= '<div>Área: <b>'.$map->property->size.' m²</b></div>'; break;
                                case 'dimensions': $html .= '<div>Dimensões: <b>'.$map->property->dimensions.'</b></div>'; break;
                                case 'situation': $html .= '<div>Situação: <b>'.($map->property->situation == 'AVAILABLE' ? 'Ativo' : 'Bloqueado').'</b></div>'; break;
                                case 'numero_matricula': $html .= '<div>Número da Matrícula: <b>'.$map->property->numero_matricula.'</b></div>'; break;
                                case 'cadastro_imobiliario': $html .= '<div>Cadastro Imobiliário: <b>'.$map->property->cadastro_imobiliario.'</b></div>'; break;
                                case 'status': 
                                    if($map->property->proposals_actives->count() == 0) {
                                        $html .= '<div>Status: <b>Disponível</b></div>'; 
                                    } else {
                                        $html .= '<div>Status: <b>'.getProposalStatusName($map->property->proposals_actives->first()->status).'</b></div>'; 
                                    }                                    
                                break;
                            }
                        }                        
                    }
                    if($map->property->situation == 'AVAILABLE') {
                        if(!$map->property->proposal_sold) {
                            if($map->property->proposals_actives->count() < 3 && !$map->property->proposal_sold) {
                                if($project->fields) $html .= '<hr>';
                                $html .= "<a href='".route('proposals.create')."?imovel=".$map->property->id."' target='_BLANK'>Criar proposta</a>";
                            }
                        }
                    }
                    $html .= '</div>';
                ?>

                @if($map->property->proposals_actives->count() > 0)
                    @if(isset($colors[$map->property->proposals_actives->first()->status]))
                        <area shape="{{ $map->shape }}" coords="{{ $map->coordinates }}" title="{{ $html }}" data-maphilight='{ "stroke": true, "fillColor": "<?php echo $colors[$map->property->proposals_actives->first()->status]; ?>", "fillOpacity": 0.6, "alwaysOn": true }'>
                    @else
                        <area shape="{{ $map->shape }}" coords="{{ $map->coordinates }}" title="{{ $html }}">
                    @endif
                @else
                    @if($map->property->situation == 'BLOCKED')
                        <area shape="{{ $map->shape }}" coords="{{ $map->coordinates }}" title="{{ $html }}" data-maphilight='{ "stroke": true, "fillColor": "79BCCD", "fillOpacity": 0.6, "alwaysOn": true }'>
                    @else
                        <area shape="{{ $map->shape }}" coords="{{ $map->coordinates }}" title="{{ $html }}">
                    @endif
                @endif
            @else
                
            @endif
        @endforeach
    </map>
@endsection
@section('js')
    <script src="{{ asset('highlight/jquery.maphilight.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/qtip2/3.0.3/jquery.qtip.min.js"></script>
    <script>
        window.onload = $('.map').maphilight();
        $(function initPage(){
            $('.map').maphilight();
            $('[title]').qtip({
                position: {
                    my: 'top left',
                    at: 'bottom right',
                },
                show: {
                    solo: true
                },
                hide: 'unfocus',
                style: {
                    classes: 'qtip-light qtip-shadow'
                }
            });
        });
    </script>
@endsection