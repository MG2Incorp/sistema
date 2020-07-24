@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Imobiliária {{ $company->name }}</div>
                    <div class="card-body">
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
                                @foreach($company->users->where('id', '!=', env('SUPERADMIN')) as $user)
                                    <tr class="text-center">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->creci }}</td>
                                        <td align="center">
                                            <a href="{{ route('users.edit', $user->id) }}?imobiliaria={{ $company->id }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <h6>Empreendimentos</h6>
                @foreach($company->projects as $project)
                    <div class="card mb-4">
                        <div class="card-header">{{ $project->name }}</div>
                        <div class="card-body">
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
                                    @foreach($project->users->where('id', '!=', env('SUPERADMIN')) as $user)
                                        <tr class="text-center">
                                            <td>{{ $user->name }}</td>
                                            <td>{{ $user->email }}</td>
                                            <td>{{ $user->phone }}</td>
                                            <td>{{ $user->creci }}</td>
                                            <td align="center">
                                                <a href="{{ route('users.edit', $user->id) }}?imobiliaria={{ $company->id }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection