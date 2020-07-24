@extends('layouts.app')
@section('css')
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card box border-warning">
                    <div class="card-header">Associar usuário</div>
                    <div class="card-body">
                        <form action="{{ action('UserController@attach') }}" method="POST">
                            {{ csrf_field() }}
                            <input type="hidden" name="company" value="{{ $company }}">
                            <input type="hidden" name="user_id" value="{{ $user->id }}">
                            <div class="form-group">
                                <h5 class="text-center">O usuário com CPF {{ $cpf }} já está cadastrado. Deseja associá-lo com a imobiliária? </h5>
                            </div>
                            <table class="table table-hover table-bordered table-sm">
                                <thead>
                                    <tr class="text-center">
                                        <th>Nome</th>
                                        <th width="15%">CPF</th>
                                        <th>E-Mail</th>
                                        <th width="15%">Telefone</th>
                                        <th width="10%">Creci</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class="text-center">
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->cpf }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ $user->creci }}</td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="submit" class="btn btn-success float-right">Vincular usuário</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
@endsection