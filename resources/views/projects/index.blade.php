@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12">
                <div class="card mb-4">
                    <div class="card-header">Empreendimentos</div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12">
                <div class="card box border-warning mb-4">
                    <div class="card-body">
                        <div class="form-inline justify-content-between">
                            <div class="form-group">
                                <a href="{{ route('projects.create') }}" class="btn btn-info">Adicionar empreendimento</a>
                            </div>
                            <div class="form-group float-right">
                                <div class="input-group">
                                    <input type="text" name="" id="" class="form-control" placeholder="Buscar">
                                    <span class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fas fa-search"></i></button></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <table class="table table-sm table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th width="30%">Nome</th>
                                    <th width="15%">CNPJ</th>
                                    <th width="10%">Entrega</th>
                                    <th width="10%">Status</th>
                                    <th>Local</th>
                                    <th width="10%" class="text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($projects as $project)
                                    <tr>
                                        <td>{{ $project->name }}</td>
                                        <td>{{ $project->cnpj }}</td>
                                        <td>{{ formatData($project->finish_at) }}</td>
                                        <td>{{ $project->status }}</td>
                                        <td>{{ $project->local }}</td>
                                        <td align="center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-secondary btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                                <div class="dropdown-menu dropdown-menu-right">
                                                    <a class="dropdown-item" href="{{ route('projects.edit', $project->id) }}">Editar</a>
                                                    <a class="dropdown-item" href="{{ route('projects.delete', $project->id) }}">Excluir</a>
                                                    <div class="dropdown-divider"></div>
                                                    @foreach($project->companies as $company)
                                                        <a class="dropdown-item" href="{{ route('projects.company.contract.send', ['contract' => $company->pivot->id]) }}">{{ $company->name }} - {{ $company->pivot->email_sent ? 'Reenviar' : 'Enviar' }} contrato por e-mail</a>
                                                    @endforeach
                                                    <div class="dropdown-divider"></div>
                                                    <button type="button" class="dropdown-item pointer" data-toggle="modal" data-target="#leads{{ $project->id }}">Leads</button>
                                                    <div class="dropdown-divider"></div>
                                                    <a class="dropdown-item" href="{{ route('lead', $project->url) }}" target="_BLANK">Visualizar link do empreendimento</a>
                                                    <a class="dropdown-item" href="{{ route('map', $project->url) }}" target="_BLANK">Visualizar mapa do empreendimento</a>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @foreach($projects as $project)
        <div class="modal fade" id="leads{{ $project->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Leads - {{ $project->name }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        @if($project->leads->count())
                            <form class="form-inline mb-4" action="" method="GET">
                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                <input type="submit" name="export" class="btn btn-success ml-auto" value="Exportar">
                            </form>
                            <table class="table table-sm table-bordered m-0">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>E-Mail</th>
                                        <th>Celular</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($project->leads as $lead)
                                        <tr>
                                            <td>{{ $lead->name }}</td>
                                            <td>{{ $lead->email }}</td>
                                            <td>{{ $lead->cellphone }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        @else
                            <h5 class="text-center">Nenhum lead encontrado para esse emprendimento.</h5>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach
@endsection
