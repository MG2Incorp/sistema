@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.css">
    <link href="{{ asset('froala2/css/froala_editor.pkgd.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('froala2/css/froala_style.min.css') }}" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-12">
                                <form action="{{ action('ContractController@update', $contract->id) }}" method="POST" id="form_contract">
                                    @csrf
                                    <div class="row">
                                        <div class="col-12 col-sm-7">
                                            <div class="form-group">
                                                <label>Nome/Identificação do contrato</label>
                                                <input type="text" name="name" class="form-control" value="{{ $contract->name }}">
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-5">
                                            <div class="form-group">
                                                <label>Empreendimento</label>
                                                <select name="project" class="form-control">
                                                    <option value="">Selecione...</option>
                                                    @foreach($projects as $project)
                                                        <option value="{{ $project->id }}" {{ $project->id == $contract->project_id ? 'selected' : '' }}>{{ $project->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <ul class="nav nav-pills nav-justified mb-1" id="myTab" role="tablist">
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab1" role="tab" aria-controls="tab1" aria-selected="true">Proponente</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab2" role="tab" aria-controls="tab2" aria-selected="false">Cônjuge</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab3" role="tab" aria-controls="tab3" aria-selected="false">Proposta</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab4" role="tab" aria-controls="tab4" aria-selected="true">Pagamento</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab5" role="tab" aria-controls="tab5" aria-selected="false">Empreendimento</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab6" role="tab" aria-controls="tab6" aria-selected="false">Imóvel</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab7" role="tab" aria-controls="tab7" aria-selected="false">Blocos</a></li>
                                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#tab8" role="tab" aria-controls="tab8" aria-selected="false">Imobiliária</a></li>
                                        </ul>
                                        <div class="tab-content" id="myTabContent">
                                            <div class="tab-pane fade" id="tab1" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('proponente') as $key => $field)
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-secondary btn-sm dropdown-toggle mb-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $field }}</button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                    @for($i = 1; $i < 5; $i++)
                                                                        <button type="button" class="dropdown-item add" href="#" data-field="{%{{ $key }}-{{ $i }}%}">Proponente {{ $i }}</button>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab2" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('conjuge') as $key => $field)
                                                            <div class="dropdown d-inline">
                                                                <button class="btn btn-secondary btn-sm dropdown-toggle mb-1" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $field }}</button>
                                                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                                                    @for($i = 1; $i < 5; $i++)
                                                                        <button type="button" class="dropdown-item add" href="#" data-field="{%{{ $key }}-{{ $i }}%}">Proponente {{ $i }}</button>
                                                                    @endfor
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab3" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('proposta') as $key => $field)
                                                            <button class="btn btn-secondary btn-sm mb-1 add" type="button" data-field="{%{{ $key }}%}">{{ $field }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab4" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('pagamento') as $key => $field)
                                                            <button class="btn btn-secondary btn-sm mb-1 add" type="button" data-field="{%{{ $key }}%}">{{ $field }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab5" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('empreendimento') as $key => $field)
                                                            <button class="btn btn-secondary btn-sm mb-1 add" type="button" data-field="{%{{ $key }}%}">{{ $field }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab6" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('imovel') as $key => $field)
                                                            <button class="btn btn-secondary btn-sm mb-1 add" type="button" data-field="{%{{ $key }}%}">{{ $field }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab7" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('blocos') as $key => $field)
                                                            <button class="btn btn-secondary btn-sm mb-1 add" type="button" data-field="{%INICIO_{{ $key }}%}<br>{%FIM_{{ $key }}%}">{{ $field }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="tab-pane fade" id="tab8" role="tabpanel">
                                                <div class="card">
                                                    <div class="card-body">
                                                        @foreach(getFormFields('imobiliaria') as $key => $field)
                                                            <button class="btn btn-secondary btn-sm mb-1 add" type="button" data-field="{%{{ $key }}%}">{{ $field }}</button>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label>Contrato</label>
                                        <textarea name="content" class="form-control summernote editor" rows="5" style="resize: none">{{ $contract->content }}</textarea>
                                    </div>
                                    <button type="submit" class="btn btn-success float-right">Salvar template</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/codemirror.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.25.0/mode/xml/xml.min.js"></script>
    <script type="text/javascript" src="{{ asset('froala2/js/froala_editor.pkgd.min.js') }}"></script>

    <script>
        $(document).ready(function(){
            $('.editor').froalaEditor({
                heightMin: 300, heightMax: 300, charCounterCount: true, language: 'pt_br', placeholderText: 'Escreva aqui o texto do contrato.',
                toolbarButtons: ['fullscreen', 'bold', 'italic', 'underline', 'strikeThrough', 'subscript', 'superscript', '|', 'fontFamily', 'fontSize', 'color', 'inlineClass', 'inlineStyle', 'paragraphStyle', 'lineHeight', '|', 'paragraphFormat', 'align', 'formatOL', 'formatUL', 'outdent', 'indent', 'quote', 'insertLink', 'insertImage', 'insertTable', '|', 'emoticons', 'fontAwesome', 'specialCharacters', 'insertHR', 'selectAll', 'clearFormatting', '|', 'print', 'html', '|', 'undo', 'redo']
            });
		    $(".fr-wrapper a").remove();

            $('.editor').on('froalaEditor.blur', function (e, editor) {
                $('.editor').froalaEditor('selection.save');
            });

            $(document).on('click', '.add', function(){
                $('.editor').froalaEditor('selection.restore');
                $('.editor').froalaEditor('html.insert', $(this).attr('data-field'), true);
            });

            $("#form_contract").validate({
                rules: {
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    project: { required: true, normalizer: function(value) { return $.trim(value); } },
                    content: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection