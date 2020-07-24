@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card box border-warning mb-4">
                    <div class="card-header">Gerenciar templates de contratos</div>
                    <div class="card-body">
                        <div class="form-inline justify-content-between">
                            <div class="form-group">
                                <a href="{{ route('contracts.create') }}" class="btn btn-info">Criar template de contrato</a>
                            </div>
                            <div class="form-group float-right">
                                <div class="input-group">
                                    <input type="text" name="" id="" class="form-control" placeholder="Buscar">
                                    <span class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fas fa-search"></i></button></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @foreach($projects as $project)
                    <div class="card mb-1">
                        <div class="card-header">{{ $project->name }}</div>
                        <div class="card-body">
                            <table class="table table-sm table-bordered mb-0">
                                <tbody>
                                    @foreach($project->contracts as $contract)
                                        <tr>
                                            <td>{{ $contract->name }}</td>
                                            <td></td>
                                            <td width="10%" class="text-center">
                                                <a href="{{ route('contracts.edit', $contract->id) }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
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