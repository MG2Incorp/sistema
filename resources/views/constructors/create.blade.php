@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Cadastrar incorporadora</div>
                    <div class="card-body">
                        <form action="{{ action('ConstructorController@store') }}" method="POST" id="form_constructor">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" name="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>CNPJ</label>
                                        <input type="text" name="cnpj" class="form-control cnpj">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Responsável</label>
                                        <input type="text" name="manager" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>E-Mail</label>
                                        <input type="email" name="email" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>CPF</label>
                                        <input type="text" name="cpf" class="form-control cpf">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Celular</label>
                                        <input type="text" name="cellphone" class="form-control celular">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <button type="submit" class="btn btn-success">Cadastrar incorporadora</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $("#form_constructor").validate({
                rules: {
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cnpj: { required: true, normalizer: function(value) { return $.trim(value); } },
                    manager: { required: true, normalizer: function(value) { return $.trim(value); } },
                    email: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cpf: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection