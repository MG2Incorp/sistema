@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Cadastrar imobiliária</div>
                    <div class="card-body">
                        <form action="{{ action('CompanyController@store') }}" method="POST" id="form_company">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" name="name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Creci</label>
                                        <input type="text" name="creci" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="text" name="telephone" class="form-control telefone">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Celular</label>
                                        <input type="text" name="cellphone" class="form-control celular">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>CNPJ</label>
                                        <input type="text" name="cnpj" class="form-control cnpj" value="{{ isset($cnpj) ? $cnpj : '' }}">
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
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Creci</label>
                                        <input type="text" name="creci_user" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>CEP</label>
                                        <input type="text" name="zipcode" class="form-control cep" id="cep">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Logradouro</label>
                                        <input type="text" name="street" class="form-control" id="logradouro">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Número</label>
                                        <input type="text" name="number" class="form-control">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Complemento</label>
                                        <input type="text" name="complement" class="form-control">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input type="text" name="district" class="form-control" id="bairro">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input type="text" name="city" class="form-control" id="cidade">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select name="state" class="form-control" id="uf">
                                            <option value="">Selecione...</option>
                                            @foreach(getStates() as $state)
                                                <option value="{{ $state }}">{{ $state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Cadastrar imobiliária</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script>
        function limpa_formulário_cep() { $("#logradouro").val(""); $("#bairro").val(""); $("#cidade").val(""); $("#uf").val(""); }

        $(document).ready(function(){
            $(document).on('keyup', "#cep", function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep != "") {
                    var validacep = /^[0-9]{8}$/;
                    if (cep.length == 8) {
                        if(validacep.test(cep)) {
                            $("#logradouro").val("...");
                            $("#bairro").val("...");
                            $("#cidade").val("...");
                            $("#uf").val("...");
                            $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                                if (!("erro" in dados)) {
                                    $("#logradouro").val(dados.logradouro);
                                    $("#bairro").val(dados.bairro);
                                    $("#cidade").val(dados.localidade);
                                    $("#uf").val(dados.uf);
                                } else { limpa_formulário_cep(); }
                                $("#address_div").show();
                            });
                        } else { limpa_formulário_cep(); alert("Formato de CEP inválido."); }
                    }
                } else { limpa_formulário_cep(); }
            });

            $("#form_company").validate({
                rules: {
                    email: { required: true, email: true, normalizer: function(value) { return $.trim(value); } },
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    creci: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cnpj: { required: true, normalizer: function(value) { return $.trim(value); } },
                    manager: { required: true, normalizer: function(value) { return $.trim(value); } },
                    telephone: { required: true, normalizer: function(value) { return $.trim(value); } },
                    cellphone: { required: true, normalizer: function(value) { return $.trim(value); } },
                    creci_user: { required: true, normalizer: function(value) { return $.trim(value); } },
                    zipcode: { required: true, normalizer: function(value) { return $.trim(value); } },
                    street: { required: true, normalizer: function(value) { return $.trim(value); } },
                    number: { required: true, normalizer: function(value) { return $.trim(value); } },
                    complement: { required: true, normalizer: function(value) { return $.trim(value); } },
                    district: { required: true, normalizer: function(value) { return $.trim(value); } },
                    city: { required: true, normalizer: function(value) { return $.trim(value); } },
                    state: { required: true, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });
        });
    </script>
@endsection