@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                @foreach(Auth::user()->projects as $project)
                    <div class="card mb-1">
                        <div class="card-header">{{ $project->name }}</div>
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <form action="{{ action('UserController@create') }}" method="GET">
                                            <div class="input-group">
                                                <input type="hidden" name="empreendimento" value="{{ $project->id }}">
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
                        @if($users->filter(function ($value, $key) use ($project) { return $value->attachs->contains('project_id', $project->id); })->count())
                            <div class="card-body table-responsive">
                                <table class="table table-hover table-bordered table-sm m-0">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Nome</th>
                                            <th>E-Mail</th>
                                            <th width="15%">Celular</th>
                                            <th width="10%">Creci</th>
                                            <th width="12%">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $user)
                                            @if($user->attachs->contains('project_id', $project->id))
                                                <tr class="text-center">
                                                    <td>{{ $user->name }}</td>
                                                    <td>{{ $user->email }}</td>
                                                    <td>{{ $user->phone }}</td>
                                                    <td>{{ $user->creci }}</td>
                                                    <td align="center">
                                                        <a href="{{ route('users.edit', $user->id) }}?empreendimento={{ $project->id }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
                                                        <a href="{{ route('users.delete', $user->id) }}?empreendimento={{ $project->id }}" class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></a>
                                                    </td>
                                                </tr>
                                            @endif
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