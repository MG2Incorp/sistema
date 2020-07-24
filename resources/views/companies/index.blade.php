@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Gerenciar imobiliárias</div>
                    <div class="card-body">
                        <div class="row justify-content-between">
                            <div class="col-12 col-sm-5">
                                <div class="form-group">
                                    @if(getRoleIndex(Auth::user()->role) > 1)
                                        <form action="{{ action('CompanyController@create') }}" method="GET">
                                            <div class="input-group">
                                                <input type="text" class="form-control cnpj" placeholder="Digite o CNPJ da imobiliária..." name="cnpj">
                                                <div class="input-group-append"><button class="btn btn-outline-secondary" type="submit">Adicionar imobiliária</button></div>
                                            </div>
                                        </form>
                                    @endif
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
                    <!-- <div class="card-body">
                        <div class="form-inline justify-content-between">
                            <div class="form-group">
                                @if(getRoleIndex(Auth::user()->role) > 1)
                                    <a href="{{ route('companies.create') }}" class="btn btn-info">Adicionar nova imobiliária</a>
                                @endif
                            </div>
                            <div class="form-group float-right">
                                <div class="input-group">
                                    <input type="text" name="" id="" class="form-control" placeholder="Buscar">
                                    <span class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fas fa-search"></i></button></span>
                                </div>
                            </div>
                        </div>
                    </div> -->
                    <div class="card-body table-responsive">
                       <table class="table table-hover table-bordered table-sm m-0">
                           <thead>
                               <tr>
                                   <th width="20%">Nome</th>
                                   <th width="20%">Responsável</th>
                                   <th width="10%">Creci</th>
                                   <th width="25%">CNPJ</th>
                                   <th width="10%" class="text-center">Ações</th>
                               </tr>
                           </thead>
                           <tbody>
                                @foreach($companies as $company)
                                    <tr>
                                        <td>{{ $company->name }}</td>
                                        <td>{{ $company->manager }}</td>
                                        <td>{{ $company->creci }}</td>
                                        <td>{{ $company->cnpj }}</td>
                                        <td align="center">
                                            @if(getRoleIndex(Auth::user()->role) > 1)
                                                <a href="{{ route('companies.show', $company->id) }}" class="btn btn-sm btn-info text-light"><i class="fas fa-eye"></i></a>
                                                <a href="{{ route('companies.edit', $company->id) }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
                                                <a href="{{ route('companies.delete', $company->id) }}" class="btn btn-sm btn-danger"><i class="far fa-trash-alt"></i></button></a>
                                            @endif
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
@endsection
@section('js')
@endsection