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
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Empresa</label>
                                        <select name="company" class="form-control" id="company">
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}" {{ $user->company_id == $company->id ? 'selected' : '' }}>{{ $company->name }}</option>
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
                            <div class="card border-bottom">
                                <div class="card-header" id="heading{{ $project->id }}">Permissões - {{ $project->name }}</div>
                                <div class="card-body">
                                    <input type="hidden" name="project_id" value="{{ $project->id }}">
                                    @foreach(getPermissions($user->role) as $key => $permission)
                                        <div class="form-check form-check-inline">
                                            <div class="custom-control custom-checkbox">
                                                <input {{ $user->checkPermission($project->id, [$permission]) ? 'checked' : '' }} type="checkbox" class="custom-control-input check_perm" name="permissions[]" value="{{ $permission }}" id="customCheck{{ $project->id }}-{{ $permission }}">
                                                <label class="custom-control-label" for="customCheck{{ $project->id }}-{{ $permission }}">{{ getPermissionName($permission) }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
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
        $(document).ready(function() {
            $("#form_user").validate({
                rules: {
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    phone: { required: true, normalizer: function(value) { return $.trim(value); } },
                    company: { required: true, normalizer: function(value) { return $.trim(value); } },
                    creci: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection