@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card box border-warning">
                    <div class="card-header">Editar usuário</div>
                    <div class="card-body">
                        <form action="{{ action('UserController@update', $user->id) }}" method="POST" id="form_user">
                            {{ csrf_field() }}
                            <input type="hidden" name="company" value="{{ $company }}">
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" name="name" class="form-control" value="{{ $user->name }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Celular</label>
                                        <input type="text" name="phone" class="form-control celular" value="{{ $user->phone }}">
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
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Cargo</label>
                                        <select name="role" id="role" class="form-control">
                                            <option value="">Selecione...</option>
                                            @foreach(getRoles() as $key => $role)
                                                @if(getRoleIndex(Auth::user()->role) >= $key)
                                                    <option value="{{ $role }}" {{ $role == $user->role ? 'selected' : '' }}>{{ getRoleName($role) }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                @if(isset($constructors))
                                    <div class="col-12 col-sm-3" id="div_constructors" style="display: none">
                                        <div class="form-group">
                                            <label>Incorporadora</label>
                                            <select name="constructor" class="form-control">
                                                @foreach($constructors as $constructor)
                                                    <option value="{{ $constructor->id }}">{{ $constructor->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <hr>
                            @foreach($projects as $project)
                                <div class="card border-bottom mb-4">
                                    <div class="card-header" id="heading{{ $project->id }}">Permissões - {{ $project->name }}</div>
                                    <div class="card-body">
                                        @if($user->projects->contains('id', $project->id) && $user->user_projects->where('project_id', $project->id)->first()->company_id != $company)
                                            <label class="form-check-label">Esse usuário já foi vinculado a esse empreendimento pela imobiliária {{ @$user->user_projects->where('project_id', $project->id)->first()->company->name }}.</label>
                                        @else
                                            <div class="form-group">
                                                <div class="form-check">
                                                    <input {{ $user->getPermissionsNew($project->id, $company)->count() ? 'checked' : '' }} class="form-check-input" type="checkbox" value="{{ $project->id }}" name="projects[]">
                                                    <label class="form-check-label">Vincular o usuário a esse empreendimento</label>
                                                </div>
                                            </div>
                                            <hr>
                                            @foreach(getPermissions('ALL') as $key => $role)
                                                @foreach($role as $permission)
                                                    <div class="form-check form-check-inline">
                                                        <div class="custom-control custom-checkbox">
                                                            <input {{ $user->checkPermissionNew($project->id, $company, [$permission]) ? 'checked' : '' }} type="checkbox" class="custom-control-input check_perm" name="permissions[{{ $project->id }}][]" data-role="{{ $key }}" value="{{ $permission }}" id="customCheck{{ $project->id }}-{{ $permission }}">
                                                            <label class="custom-control-label" for="customCheck{{ $project->id }}-{{ $permission }}">{{ getPermissionName($permission) }}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <hr>
                            <button type="submit" class="btn btn-success float-right">Editar usuário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript">
        function block() {
            var role = $("#role").val();

            $(".check_perm").prop('disabled', false);

            if(role == 'INCORPORATOR') {
                $("#div_constructors").show();
            } else {
                $("#div_constructors").hide();
            }

            switch (role) {
                case 'ADMIN': break;
                case 'INCORPORATOR': $("input[data-role='ADMIN']").prop('disabled', true).prop('checked', false); break;
                case 'COORDINATOR':
                    $("input[data-role='ADMIN']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='INCORPORATOR']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='ENGINEER']").prop('disabled', true).prop('checked', false);
                break;
                case 'AGENT':
                    $("input[data-role='ADMIN']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='INCORPORATOR']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='COORDINATOR']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='ENGINEER']").prop('disabled', true).prop('checked', false);
                break;
                case 'ENGINEER':
                    $("input[data-role='ADMIN']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='INCORPORATOR']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='COORDINATOR']").prop('disabled', true).prop('checked', false);
                    $("input[data-role='AGENT']").prop('disabled', true).prop('checked', false);
                break;
                default: break;
            }
        }
        $(document).ready(function() {

            block();

            $(document).on('change', '#role', function(event) {
                block();
            });

            $("#form_user").validate({
                rules: {
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    phone: { required: true, normalizer: function(value) { return $.trim(value); } },
                    company: { required: true, normalizer: function(value) { return $.trim(value); } },
                    creci: { required: true, normalizer: function(value) { return $.trim(value); } },
                    role: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection