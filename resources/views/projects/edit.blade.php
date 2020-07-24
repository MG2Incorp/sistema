@extends('layouts.app')
@section('css')
    <link href="{{ asset('date/datedropper.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/choices.css') }}">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1">Editar empreendimento</a></li>
                    <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2">Mapa</a></li>
                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="tab1" role="tabpanel">
                        <div class="card border-top-0">
                            <div class="card-body">
                                <form action="{{ action('ProjectController@update', $project->id) }}" method="POST" enctype="multipart/form-data" id="form_project">
                                    {{ csrf_field() }}
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Razão Social</label>
                                                <input type="text" name="social_name" class="form-control" value="{{ $project->social_name }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="form-group">
                                                <label>CNPJ</label>
                                                <input type="text" name="cnpj" class="form-control cnpj" value="{{ $project->cnpj }}">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-6">
                                            <div class="form-group">
                                                <label>Nome</label>
                                                <input type="text" name="name" class="form-control" value="{{ $project->name }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="form-group">
                                                <label>Previsão de entrega</label>
                                                <input type="text" name="finish" class="form-control data" data-max-year="2100" data-init-set="false" data-format="d/m/Y" data-lang="pt" data-large-mode="true" data-large-default="true" data-modal="true" value="{{ formatData($project->finish_at) }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="form-group">
                                                <label>Status da construção</label>
                                                <select class="form-control" name="status">
                                                    <option value="Em obra" {{ $project->status == 'Em obra' ? 'selected' : '' }}>Em obra</option>
                                                    <option value="Concluído" {{ $project->status == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-2">
                                            <div class="form-group">
                                                <label>Tipo</label>
                                                <select class="form-control" name="type">
                                                    <option value="Andar" {{ $project->type == 'Andar' ? 'selected' : '' }}>Construção</option>
                                                    <option value="Quadra" {{ $project->type == 'Quadra' ? 'selected' : '' }}>Loteamento</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label>Local</label>
                                                <input type="text" name="local" class="form-control" id="local" autocomplete="off" value="{{ $project->local }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-2">
                                            <div class="form-group">
                                                <label>Ativar simulador?</label>
                                                <select name="simulator" class="form-control">
                                                    <option value="1" {{ $project->simulator == 1 ? 'selected' : '' }}>Sim</option>
                                                    <option value="0" {{ $project->simulator == 0 ? 'selected' : '' }}>Não</option>
                                                </select>
                                            </div>
                                        </div>

                                        <div class="col-12 col-sm-4" id="div_simulator" style="display: {{ $project->simulator ? 'block' : 'none' }}">
                                            <div class="row">
                                                <div class="col-12 col-sm-5">
                                                    <div class="form-group">
                                                        <label>Taxa</label>
                                                        <input type="text" name="fee" class="form-control money" value="{{ formatMoney($project->fee) }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-7">
                                                    <div class="form-group">
                                                        <label>Porcentagem Mínima</label>
                                                        <div class="input-group">
                                                            <input type="text" name="minimum_percentage" class="form-control money" value="{{ formatMoney($project->minimum_percentage) }}">
                                                            <div class="input-group-prepend"><span class="input-group-text">%</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label>Foto</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="inputGroupFile04" name="file">
                                                        <label class="custom-file-label" for="inputGroupFile04">Selecionar</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_photo"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                                <small class="form-text text-muted">Recomendado: Larg: 150px / Alt: 190px</small>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="form-group">
                                                <label>Tempo para expiração</label>
                                                <div class="input-group">
                                                    <input type="text" name="time" class="form-control" value="{{ $project->expiration_time }}">
                                                    <div class="input-group-prepend"><span class="input-group-text">h</span></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-2">
                                            <div class="form-group">
                                                <label>Comissão</label>
                                                <div class="input-group">
                                                    <input type="text" name="comission" class="form-control money" value="{{ formatMoney($project->comission) }}">
                                                    <div class="input-group-prepend"><span class="input-group-text">%</span></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label>Imagem para Proposta</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="inputGroupFile06" name="file2">
                                                        <label class="custom-file-label" for="inputGroupFile06">Selecionar</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_photo2"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <small class="form-text text-muted">Recomendado: Larg: 280px / Alt: 150px</small>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="form-group">
                                                <label>Observações de registro</label>
                                                <input type="text" name="notes" class="form-control" value="{{ $project->notes }}">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <label>Blocos</label>
                                    <div class="row" id="buildings">
                                        <div class="col-12 col-sm-1">
                                            <div class="form-group">
                                                <button type="button" class="btn btn-info btn-block" id="add"><i class="fas fa-plus"></i></button>
                                            </div>
                                        </div>
                                        @foreach($project->buildings as $building)
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <input type="text" name="old_buildings[{{ $building->id }}]" class="form-control" value="{{ $building->name }}">
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    <hr>
                                    <label>Imobiliárias</label>
                                    <div class="row">
                                        <div class="col-12">
                                            <select class="form-control js-choice2" name="companies[]" required multiple>
                                                @foreach($companies as $company)
                                                    <option value="{{ $company->id }}" {{ $project->companies->contains('id', $company->id) ? 'selected' : '' }}>{{ $company->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <label>Índices</label>
                                    <div class="row">
                                        <div class="col-12">
                                            @php $indexes = explode(',', $project->indexes); @endphp
                                            <select class="form-control js-choice2" name="indexes[]" required multiple>
                                                @foreach(getCorrectionIndexes() as $index)
                                                    <option value="{{ $index }}" {{ in_array($index, $indexes) ? 'selected' : '' }}>{{ $index }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label>Leading - Background</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="inputGroupFile05" name="bg">
                                                        <label class="custom-file-label" for="inputGroupFile05">Selecionar</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_bg"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-3">
                                            <div class="form-group">
                                                <label>Ativar chat?</label>
                                                <select name="chat" class="form-control" id="chat">
                                                    <option value="1" {{ $project->chat == 1 ? 'selected' : '' }}>Sim</option>
                                                    <option value="0" {{ $project->chat == 0 ? 'selected' : '' }}>Não</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4" style="display: {{ $project->chat ? 'block' : 'none' }}" id="div_codigo_jivochat">
                                            <div class="form-group">
                                                <label>Código Jivochat (Widget ID apenas)</label>
                                                <input type="text" name="chat_code" class="form-control" value="{{ $project->chat_code }}">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    <button type="submit" class="btn btn-success">Salvar empreendimento</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="tab2" role="tabpanel">
                        <div class="card border-top-0">
                            <div class="card-body">
                                <form action="{{ action('ProjectController@map') }}" method="POST" enctype="multipart/form-data">
                                    {{ csrf_field() }}
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                    <div class="form-group">
                                        <label>Imagem do Mapa (Tamanho recomendado: 1920px x 1080px)</label>
                                        <div class="input-group">
                                            <div class="custom-file">
                                                <input type="file" class="custom-file-input" id="inputGroupFile07" name="map">
                                                <label class="custom-file-label" for="inputGroupFile07">Selecionar</label>
                                            </div>
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_photo3"><i class="fas fa-eye"></i></button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="alert alert-primary" role="alert">
                                        Para a obtenção das coordenadas, <a href="https://www.image-map.net/" class="alert-link" target="_BLANK">clique aqui</a>.
                                    </div>
                                    <div class="form-group">
                                        <label>Mapeamento</label>
                                        <table class="table table-sm table-bordered table-hover m-0 text-center">
                                            <thead>
                                                <tr>
                                                    <th width="10%">Bloco</th>
                                                    <th width="10%">Andar</th>
                                                    <th width="10%">Número</th>
                                                    <th width="15%">Forma</th>
                                                    <th>Coordenadas</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($project->properties as $property)
                                                    <tr>
                                                        <td>{{ $property->block->building->name }} <input type="hidden" name="properties[]" value="{{ $property->id }}"></td>
                                                        <td>{{ $property->block->label }}</td>
                                                        <td>{{ $property->number }}</td>
                                                        <td>
                                                            <select name="shapes[{{ $property->id }}]" class="form-control">
                                                                <option value="">Selecione...</option>
                                                                <option value="rect" {{ $property->map && $property->map->shape == 'rect' ? 'selected' : '' }}>Rect</option>
                                                                <option value="poly" {{ $property->map && $property->map->shape == 'poly' ? 'selected' : '' }}>Poly</option>
                                                                <option value="circle" {{ $property->map && $property->map->shape == 'circle' ? 'selected' : '' }}>Circle</option>
                                                            </select>
                                                        </td>
                                                        <td><input type="text" name="coords[{{ $property->id }}]" class="form-control" value="{{ $property->map ? $property->map->coordinates : '' }}"></td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    <hr>
                                    <button type="submit" class="btn btn-success">Salvar mapeamento</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_photo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="image_thumb" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->photo) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_photo2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="image_thumb2" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->photo2) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_photo3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="image_thumb3" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->map) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_bg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="bg_thumb" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->background_image) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('date/datedropper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/choices.js') }}"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCA_j74BkWoTK9_q-vGr2qfjU8A8UIp_fA&amp;libraries=places"></script>
    <script>
        function init() {
			var input = document.getElementById('local');
            var autocomplete = new google.maps.places.Autocomplete(input);
		}

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#image_thumb').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#bg_thumb').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURL3(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#image_thumb2').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURL4(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#image_thumb3').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function(){
            init();

            const choices2 = new Choices('.js-choice2', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar' });

            $(".data").dateDropper();

            $("#inputGroupFile04").change(function() {
                readURL(this);
            });

            $("#inputGroupFile05").change(function() {
                readURL2(this);
            });

            $("#inputGroupFile06").change(function() {
                readURL3(this);
            });

            $("#inputGroupFile07").change(function() {
                readURL4(this);
            });

            $(document).on('click', '#add', function(){
                $("#buildings").append('<div class="col-12 col-sm-3">\
                                            <div class="form-group">\
                                                <div class="input-group">\
                                                    <input type="text" name="buildings[]" class="form-control">\
                                                    <div class="input-group-append"><button type="button" class="btn btn-danger remove"><i class="far fa-trash-alt"></i></button></div>\
                                                </div>\
                                            </div>\
                                        </div>');
            });

            $(document).on('click', '.remove', function(){
                $(this).parent().parent().parent().parent().remove();
            });

            $(document).on('change', '#simulator', function(){
                if($(this).val() == 1) {
                    $("#div_simulator").show();
                } else {
                    $("#div_simulator").hide();
                }
            });

            $(document).on('change', '#chat', function(){
                if($(this).val() == 1) {
                    $("#div_codigo_jivochat").show();
                } else {
                    $("#div_codigo_jivochat").hide();
                }
            });

            const activeChat = function(element) {
                return $("#chat").val() == 1;
            };

            $("#form_project").validate({
                rules: {
                    social_name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cnpj: { required: true, normalizer: function(value) { return $.trim(value); } },
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    finish: { required: true, normalizer: function(value) { return $.trim(value); } },
                    status: { required: true, normalizer: function(value) { return $.trim(value); } },
                    type: { required: true, normalizer: function(value) { return $.trim(value); } },
                    local: { required: true, normalizer: function(value) { return $.trim(value); } },
                    // constructor: { required: true, normalizer: function(value) { return $.trim(value); } },
                    // fee: { required: true, normalizer: function(value) { return $.trim(value); } },
                    simulator: { required: true, normalizer: function(value) { return $.trim(value); } },
                    // file: { required: true, normalizer: function(value) { return $.trim(value); } },
                    'old_buildings[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    chat_code: { required: { depends: activeChat }, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection