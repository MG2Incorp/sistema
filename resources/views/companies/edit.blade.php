@extends('layouts.app')
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">Editar imobiliária</div>
                    <div class="card-body">
                        <form action="{{ action('CompanyController@update', $company->id) }}" method="POST" id="form_company">
                            {{ csrf_field() }}
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Nome</label>
                                        <input type="text" name="name" class="form-control" value="{{ $company->name }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Creci</label>
                                        <input type="text" name="creci" class="form-control" value="{{ $company->creci }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="text" name="telephone" class="form-control telefone" value="{{ $company->telephone }}">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>CEP</label>
                                        <input type="text" name="zipcode" class="form-control cep" value="{{ $company->address ? $company->address->zipcode : '' }}" id="cep">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Logradouro</label>
                                        <input type="text" name="street" class="form-control" value="{{ $company->address ? $company->address->street : '' }}" id="logradouro">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Número</label>
                                        <input type="text" name="number" class="form-control" value="{{ $company->address ? $company->address->number : '' }}">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Complemento</label>
                                        <input type="text" name="complement" class="form-control" value="{{ $company->address ? $company->address->complement : '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input type="text" name="district" class="form-control" value="{{ $company->address ? $company->address->district : '' }}" id="bairro">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input type="text" name="city" class="form-control" value="{{ $company->address ? $company->address->city : '' }}" id="cidade">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Estado</label>
                                        <select name="state" class="form-control" id="uf">
                                            <option value="">Selecione...</option>
                                            @foreach(getStates() as $state)
                                                <option value="{{ $state }}" {{ $company->address &&  $company->address->state == $state ? 'selected' : '' }}>{{ $state }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-success">Editar imobiliária</button>
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
                    name: { required: true, normalizer: function(value) { return $.trim(value); } },
                    creci: { required: true, normalizer: function(value) { return $.trim(value); } },
                    telephone: { required: true, normalizer: function(value) { return $.trim(value); } },
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