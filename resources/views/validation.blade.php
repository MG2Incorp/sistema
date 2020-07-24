@extends('layouts.app')
@section('css')
    <style>
        .full-height { height: 100vh; }
        .flex-center { align-items: center; display: flex; justify-content: center; }
        h6 { font-weight: 400 !important }
    </style>
@endsection
@section('content')
    <div class="full-height">
        <div class="container full-height">
            <div class="row full-height justify-content-center flex-center">
                <div class="col-12 col-sm-6">
                    <div class="card">
                        <div class="card-body">
                            <div class="my-4" align="center">
                                <a href="{{ route('home') }}"><img src="{{ asset('img/logo.png') }}"></a>
                            </div>
                            <form method="get" action="">
                                <div class="form-group" align="center">
                                    <label>Número do contrato</label>
                                    <input type="text" class="form-control" name="code" required autofocus value="{{ isset($code) ? $code : '' }}">
                                </div>
                                <div class="form-group mb-0">
                                    <button type="submit" class="btn btn-outline-success btn-block">Verificar situação do contrato</button>
                                </div>
                            </form>
                            @if(isset($code))
                                <hr>
                                @if(isset($not_found))
                                    <div class="alert alert-danger mb-0" role="alert">Código não encontrado.</div>
                                @else
                                    <div>
                                        @if($contract->situation)
                                            <h4 class="text-success">CONTRATO ATIVO</h4>
                                        @else
                                            <h4 class="text-danger">CONTRATO INATIVO</h4>
                                        @endif
                                        <h6>O contrato referente a venda do:</h6>
                                        <h6>{{ $contract->project->social_name }}</h6>
                                        @if(isset($user))
                                            <h6>pela <i>{{ $contract->user->user_projects_with_trashed()->where('project_id', $contract->project->id)->first()->company->name }}, CRECI {{ $contract->user->user_projects_with_trashed()->where('project_id', $contract->project->id)->first()->company->creci }}</i>,</h6>
                                            <h6>E pelo <i>{{ $contract->user->name }}, CRECI Nº {{ $contract->user->creci }}</i>,</h6>
                                        @else
                                            <h6>pela <i>{{ $contract->company->name }}, CRECI {{ $contract->company->creci }}</i>,</h6>
                                        @endif
                                        @if($contract->situation)
                                            <h6><i>Se encontra devidamente ativo desde {{ dateString($contract->created_at) }}.</i></h6>
                                        @else
                                            @if($contract->deleted_at == null)
                                                <h6><i>Não se encontra ativo, favor entrar em contato com o responsável do empreendimento.</i></h6>
                                            @else
                                                <h6><i>Não se encontra ativo desde {{ dateString($contract->deleted_at) }}, favor entrar em contato com o responsável do empreendimento.</i></h6>
                                            @endif
                                        @endif
                                    </div>
                                @endif
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection