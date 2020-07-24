@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Gerenciar incorporadoras</div>
                    <div class="card-body">
                        <div class="form-inline justify-content-between">
                            <div class="form-group">
                                @if(getRoleIndex(Auth::user()->role) > 1)
                                    <a href="{{ route('constructors.create') }}" class="btn btn-info">Adicionar nova incorporadora</a>
                                @endif
                            </div>
                            <div class="form-group float-right">
                                <div class="input-group">
                                    <input type="text" name="" id="" class="form-control" placeholder="Buscar">
                                    <span class="input-group-append"><button class="btn btn-outline-secondary" type="button" id="button-addon2"><i class="fas fa-search"></i></button></span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive">
                       <table class="table table-hover table-bordered table-sm m-0">
                           <thead>
                               <tr>
                                   <th>Nome</th>
                                   <th width="20%">CNPJ</th>
                                   <th width="10%" class="text-center">Ações</th>
                               </tr>
                           </thead>
                           <tbody>
                                @foreach($constructors as $constructor)
                                    <tr>
                                        <td>{{ $constructor->name }}</td>
                                        <td>{{ $constructor->cnpj }}</td>
                                        <td align="center">
                                            @if(getRoleIndex(Auth::user()->role) > 1)
                                                <a href="{{ route('constructors.edit', $constructor->id) }}" class="btn btn-sm btn-warning text-light"><i class="fas fa-edit"></i></a>
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