@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('css/choices.css') }}">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card box border-warning">
                    <div class="card-header">Editar usuário</div>
                    <div class="card-body">
                        <form action="{{ action('UserController@update', $user->id) }}" method="POST">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="text" name="phone" class="form-control telefone" value="{{ $user->phone }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Empresa</label>
                                        <select name="company" class="form-control" id="company">
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" {{ $company->id == $user->company_id ? 'selected' : '' }}>{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Creci</label>
                                        <input type="text" name="creci" class="form-control" value="{{ $user->creci }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <div class="form-group">
                                        <label>Permissões de Usuário</label>
                                        <select class="form-control permissions" name="permissions[]">
                                            <option value="">Selecione...</option>
                                            @foreach($permissions->where('type', 'USER') as $permission)
                                                <option value="{{ $permission->label }}" {{ $user->user_permissions->contains('permission', $permission->label) }}>{{ $permission->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Permissões de empreendimentos</label>
                                        <div class="input-group">
                                            <select id="projects" class="form-control">
                                                <option value="">Selecione...</option>
                                                @foreach($projects as $project)
                                                    <option value="{{ $project->id }}" {{ $user->projects->contains('id', $project->id) ? 'disabled' : '' }}>{{ $project->name }}</option>
                                                @endforeach
                                            </select>
                                            <div class="input-group-append"><button type="button" class="btn btn-secondary-outline" id="add_project" disabled><i class="fas fa-plus"></i></button></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row" id="permissions">
                                @foreach($user->projects as $project)
                                    @php $perm = $user->getPermissions($project->id) @endphp
                                    <div class="col-12 col-sm-12">
                                        <div class="card mb-4">
                                            <div class="card-header"><span class="project_name">{{ $project->name }}</span><button type="button" class="btn btn-danger btn-sm float-right remove_project"><i class="fas fa-times"></i></button></div>
                                            <div class="card-body">
                                                <input type="hidden" name="projects[]" class="selected_project" value="{{ $project->id }}">
                                                <select class="form-control js-choice-old" name="access[{{ $project->id }}][]" multiple>
                                                    @foreach($permissions->where('type', 'PROJECT') as $permission)
                                                        <option value="{{ $permission->label }}" {{ $perm->contains('permission', $permission->label) ? 'selected' : '' }}>{{ $permission->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-success">Salvar usuário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-12 invisible" id="div_permissions">
        <div class="card mb-4">
            <div class="card-header"><span class="project_name"></span><button type="button" class="btn btn-danger btn-sm float-right remove_project"><i class="fas fa-times"></i></button></div>
            <div class="card-body">
                <input type="hidden" name="projects[]" class="selected_project">
                <select class="form-control js-choice" name="access[]" multiple>
                    @foreach($permissions->where('type', 'PROJECT') as $permission)
                        <option value="{{ $permission->label }}">{{ $permission->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script src="{{ asset('js/choices.js') }}"></script>
    <script type="text/javascript">
        function update(option) {
            $("#projects option").each(function(){
                $(this).prop('disabled', false);
            });

            $("#permissions .selected_project").each(function(){
                $('#projects option[value="'+$(this).val()+'"]').prop('disabled', true);
            });
        }

        $(document).ready(function() {
            const choices2 = new Choices('.js-choice-old', { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione as permissões...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar'  });

            $(document).on('click', '#add_project', function(event) {
                var clone = $("#div_permissions").clone();
                clone.removeAttr('id');
                clone.removeClass('invisible').addClass('visible');
                clone.find('.selected_project').val($("#projects").val());
                clone.find('.js-choice').attr('name', 'access['+$("#projects").val()+'][]');
                clone.find('.project_name').text($("#projects option:selected").html());
                clone.find('.js-choice').attr('id', 'js-choice'+$("#projects").val());
                $("#permissions").append(clone);

                const choices2 = new Choices('#js-choice'+$("#projects").val(), { removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione as permissões...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar'  });

                $("#projects").val('');
                $(this).prop('disabled', true);
                update();
            });

            $(document).on('change', '#projects', function(event) {
                if($(this).val() != '') {
                    $("#add_project").prop('disabled', false);
                } else {
                    $("#add_project").prop('disabled', true);
                }
            });

            $(document).on('click', '.remove_project', function(event) {
                $(this).parent().parent().parent().remove();
                update();
            });
        });
    </script>
@endsection