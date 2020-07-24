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
            @foreach($projects as $project)
                <div class="col-12 col-sm-4">
                    <div class="card box border-info mb-4">
                        <div class="card-header text-center">{{ $project->name }}</div>
                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="col-6 col-sm-6 pr-1">
                                    <a href="{{ route('map', $project->url) }}" class="card h-100 project_photo" target="_BLANK" style="background-image: url({{ asset(env('PROJECTS_IMAGES_DIR').$project->photo) }}); background-size: cover;background-repeat: no-repeat;background-position: center center"></a>
                                </div>
                                <div class="col-6 col-sm-6 pl-1">
                                    <h6 class="mb-1 p-0"><span class="badge badge-info p-2 w-100">Entrega: {{ formatData($project->finish_at) }}</span></h6>
                                    <h6 class="mb-1 p-0"><span class="badge badge-{{ $project->status == 'Concluído' ? 'success' : 'danger' }} p-2 w-100">{{ $project->status }}</span></h6>
                                    <h6 class="mb-1 p-0"><span class="badge badge-info p-2 w-100">Unidades: {{ $project->properties->count() }}</span></h6>
                                    <h6 class="mb-1 p-0"><span class="badge badge-info p-2 w-100">Reservas: {{ $project->proposals_actives->groupBy('property_id')->count() }} </span></h6>
                                    <h6 class="mb-1 p-0"><span class="badge badge-info p-2 w-100">{{ $project->local }}</span></h6>
                                    <h6 class="m-0 p-0"><button class="btn btn-success btn-sm w-100" data-toggle="modal" data-target="#docs{{ $project->id }}">Ver documentos</a></h6>
                                </div>
                            </div>
                            <div class="row justify-content-center">
                                <div class="col-6 col-sm-6 pr-1">
                                    <h6 class="m-0 p-0"><a href="{{ route('map.index') }}?empreendimento={{ $project->id }}&predio={{ @$project->buildings->first()->id }}" class="badge badge-primary p-2 w-100">Acessar</a></h6>
                                </div>
                                @if($project->simulator)
                                    <div class="col-6 col-sm-6 pl-1">
                                        <h6 class="m-0 p-0"><a href="{{ route('simulator') }}?empreendimento={{ $project->id }}" target="_BLANK" class="badge badge-success p-2 w-100">Simulador</a></h6>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal fade" id="docs{{ $project->id }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-xl" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="exampleModalLabel">Documentação - {{ $project->name }}</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            </div>
                            <div class="modal-body">
                                @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                                    <div class="card mb-4">
                                        <div class="card-header">Adicionar documento</div>
                                        <div class="card-body">
                                            <form class="row" action="{{ action('ProjectController@document') }}" method="POST" enctype="multipart/form-data">
                                                @csrf
                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                <div class="col-12 col-sm-5">
                                                    <input type="text" class="form-control" name="description">
                                                </div>
                                                <div class="col-12 col-sm-5">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" name="file" required>
                                                        <label class="custom-file-label">Selecionar arquivo</label>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <button type="submit" class="btn btn-primary">Adicionar</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                                <div class="card">
                                    <div class="card-header">Documentos adicionados</div>
                                    <div class="card-body">
                                        @if($project->documents->count())
                                            <table class="table table-hover table-sm table-bordered m-0">
                                                <thead>
                                                    <tr class="text-center">
                                                        <th width="15%">Data de Envio</th>
                                                        <th width="15%">Enviado por</th>
                                                        <th width="30%">Descrição</th>
                                                        <th>Arquivo</th>
                                                        @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                                                            <th width="10%">Remover</th>
                                                        @endif
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($project->documents as $document)
                                                        <tr class="text-center">
                                                            <td>{{ dateString($document->created_at) }}</td>
                                                            <td>{{ @$document->user->name }}</td>
                                                            <td>{{ $document->description }}</td>
                                                            <td>
                                                                <a href="{{ route('projects.download', $document->file) }}" target="_BLANK"><i class="fas fa-download"></i> Baixar arquivo</a>
                                                            </td>
                                                            @if(in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR']))
                                                                <td>
                                                                    <a href="{{ route('projects.document.delete', $document->id) }}">Remover</a>
                                                                </td>
                                                            @endif
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        @else
                                            <h6 class="text-center">Nenhum documento encontrado.</h6>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>
    </div>
@endsection
