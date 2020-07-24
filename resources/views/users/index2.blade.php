@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                @if(Auth::user()->role == 'ADMIN' || Auth::user()->role == 'INCORPORATOR')
                    <div class="card mb-1">
                        <div class="card-header">Novo @if(Auth::user()->role == 'ADMIN') administrador @else incorporador @endif</div>
                        <div class="card-body">
                            <form action="{{ action('UserController@user') }}" method="POST" id="form_new_user">
                                @csrf
                                <input type="hidden" name="type" value="{{ Auth::user()->role }}">
                                <div class="row">
                                    <div class="col-12 col-sm-4">
                                        <div class="form-group">
                                            <label>Nome</label>
                                            <input type="text" name="name" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-3">
                                        <div class="form-group">
                                            <label>E-Mail</label>
                                            <input type="email" name="email" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-2">
                                        <div class="form-group">
                                            <label>CPF</label>
                                            <input type="text" name="cpf" class="form-control cpf">
                                        </div>
                                    </div>
                                    <div class="col-12 col-sm-2">
                                        <div class="form-group">
                                            <label>Celular</label>
                                            <input type="text" name="phone" class="form-control celular">
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-success">Cadastrar novo @if(Auth::user()->role == 'ADMIN') administrador @else incorporador @endif</button>
                            </form>
                        </div>
                    </div>
                @endif
                @if(Auth::user()->role == 'ADMIN')
                    <hr>
                    <div class="card mb-1">
                        <div class="card-header">Cadastrar usuário sem imobiliária</div>
                        <div class="card-body">
                            <div class="row justify-content-start">
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <form action="{{ action('UserController@create2') }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control cpf" placeholder="Digite o CPF do usuário..." name="cpf">
                                                <div class="input-group-append"><button class="btn btn-outline-secondary" type="submit">Adicionar usuário</button></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <hr>
                        <div class="card mb-1">
                            <div class="card-header">Usuários sem imobiliária</div>
                            <div class="card-body">
                                <table class="table table-hover table-bordered table-sm m-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Nome</th>
                                            <th>E-Mail</th>
                                            <th width="15%">Celular</th>
                                            <th width="10%">Creci</th>
                                            <th width="15%">CPF</th>
                                            <th width="10%">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($avulsos->where('id', '!=', env('SUPERADMIN')) as $user)
                                            <tr class="text-center">
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone }}</td>
                                                <td>{{ $user->creci }}</td>
                                                <td>{{ $user->cpf }}</td>
                                                <td align="center">
                                                    <a href="{{ route('users.edit.free', $user->id) }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    <hr>
                @endif
                @foreach(Auth::user()->companies as $company)
                    <div class="card mb-1">
                        <div class="card-header">{{ $company->name }}</div>
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <form action="{{ action('UserController@create') }}" method="GET">
                                            <div class="input-group">
                                                <input type="hidden" name="imobiliaria" value="{{ $company->id }}">
                                                <input type="text" class="form-control cpf" placeholder="Digite o CPF do usuário..." name="cpf">
                                                <div class="input-group-append"><button class="btn btn-outline-secondary" type="submit">Adicionar usuário</button></div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <input type="text" name="" id="" class="form-control" placeholder="Buscar">
                                            <span class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fas fa-search"></i></button></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @if($company->users->count())
                            <div class="card-body table-responsive">
                                <table class="table table-hover table-bordered table-sm m-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Nome</th>
                                            <th>E-Mail</th>
                                            <th width="15%">Celular</th>
                                            <th width="10%">Creci</th>
                                            <th width="15%">CPF</th>
                                            <th width="10%">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($company->users->where('id', '!=', env('SUPERADMIN')) as $user)
                                            <tr class="text-center">
                                                <td>{{ $user->name }}</td>
                                                <td>{{ $user->email }}</td>
                                                <td>{{ $user->phone }}</td>
                                                <td>{{ $user->creci }}</td>
                                                <td>{{ $user->cpf }}</td>
                                                <td align="center">
                                                    @if(getRoleIndex(Auth::user()->role) > getRoleIndex($user->role))
                                                        <a href="{{ route('users.edit', $user->id) }}?imobiliaria={{ $company->id }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
                                                        <a onclick="return confirm('Deseja realmente desvincular esse usuário dessa imobiliária? (Obs: Todos os projetos no qual ele estiver vinculado por meio dessa imobiliária também serão desvinculados.)')" href="{{ route('users.dettach', $user->id) }}?imobiliaria={{ $company->id }}" class="btn btn-sm btn-danger text-light"><i class="fas fa-times"></i></a>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function(){
            $("#form_new_user").validate({
                rules: {
                    email: { required: true, email: true, normalizer: function(value) { return $.trim(value); } },
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cpf: { required: true, normalizer: function(value) { return $.trim(value); } },
                    phone: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection