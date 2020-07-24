@extends('layouts.app')
@section('css')
    <link href="{{ asset('date/datedropper.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/choices.css') }}">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card box border-warning">
                    <div class="card-header">Cadastrar empreendimento</div>
                    <div class="card-body">
                        <form action="{{ action('ProjectController@store') }}" method="POST" enctype="multipart/form-data" id="form_project">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Razão Social</label>
                                        <input type="text" name="social_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>CNPJ</label>
                                        <input type="text" name="cnpj" class="form-control cnpj">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Incorporadora</label>
                                        <select name="constructor" class="form-control">
                                            @foreach($constructors as $constructor)
                                                <option value="{{ $constructor->id }}">{{ $constructor->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" name="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Previsão de entrega</label>
                                        <input type="text" name="finish" class="form-control data" data-max-year="2100" data-format="d/m/Y" data-lang="pt" data-large-mode="true" data-large-default="true" data-modal="true">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Status da construção</label>
                                        <select class="form-control" name="status">
                                            <option value="Em obra">Em obra</option>
                                            <option value="Concluído">Concluído</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Tipo</label>
                                        <select class="form-control" name="type">
                                            <option value="Andar">Construção</option>
                                            <option value="Quadra">Loteamento</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Local</label>
                                        <input type="text" name="local" class="form-control" id="local" autocomplete="off">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Ativar simulador?</label>
                                        <select name="simulator" class="form-control" id="simulator">
                                            <option value="1">Sim</option>
                                            <option value="0" selected>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4" id="div_simulator" style="display: none">
                                    <div class="row">
                                        <div class="col-12 col-sm-5">
                                            <div class="form-group">
                                                <label>Taxa</label>
                                                <input type="text" name="fee" class="form-control money" value="0,00">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-7">
                                            <div class="form-group">
                                                <label>Porcentagem Mínima</label>
                                                <div class="input-group">
                                                    <input type="text" name="minimum_percentage" class="form-control money" value="0,00">
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
                                            <input type="text" name="time" class="form-control">
                                            <div class="input-group-prepend"><span class="input-group-text">h</span></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Comissão</label>
                                        <div class="input-group">
                                            <input type="text" name="comission" class="form-control money" value="0,00">
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
                                        <small class="form-text text-muted">Recomendado: Larg: 280px / Alt: 150px</small>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Observações de registro</label>
                                        <input type="text" name="notes" class="form-control">
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
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <input type="text" name="buildings[]" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <label>Imobiliárias</label>
                            <div class="row">
                                <div class="col-12">
                                    <select class="form-control js-choice2" name="companies[]" required multiple>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <hr>
                            <label>Índices</label>
                            <div class="row">
                                <div class="col-12">
                                    <select class="form-control js-choice2" name="indexes[]" required multiple>
                                        @foreach(getCorrectionIndexes() as $index)
                                            <option value="{{ $index }}">{{ $index }}</option>
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
                                            <option value="1">Sim</option>
                                            <option value="0" selected>Não</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4" style="display: none" id="div_codigo_jivochat">
                                    <div class="form-group">
                                        <label>Código Jivochat (Widget ID apenas)</label>
                                        <input type="text" name="chat_code" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-success">Cadastrar empreendimento</button>
                        </form>
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
                    <img id="image_thumb" src="" style="max-width: 100%">
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
                    <img id="image_thumb2" src="" style="max-width: 100%">
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
                    <img id="bg_thumb" src="" style="max-width: 100%">
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
                    constructor: { required: true, normalizer: function(value) { return $.trim(value); } },
                    time: { required: true, normalizer: function(value) { return $.trim(value); } },
                    comission: { required: true, normalizer: function(value) { return $.trim(value); } },
                    notes: { required: true, normalizer: function(value) { return $.trim(value); } },
                    // fee: { required: true, normalizer: function(value) { return $.trim(value); } },
                    simulator: { required: true, normalizer: function(value) { return $.trim(value); } },
                    // file: { required: true, normalizer: function(value) { return $.trim(value); } },
                    'buildings[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    chat_code: { required: { depends: activeChat }, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection