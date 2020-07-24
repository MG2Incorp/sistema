@extends('layouts.app')
@section('css')
    <style>
        .first { flex: 2 1 auto !important; }
        .second { flex: 1 1 auto !important; }
    </style>
@endsection
@section('content')
    @if(session('erros'))
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    @if(is_array(session('erros')) && count(session('erros')))
                        <div class="alert alert-danger" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <strong>Alguns erros foram encontrados:</strong>
                            {{ printa(session('erros')) }}
                        </div>
                    @else
                        <div class="alert alert-sucesso" role="alert">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            Informações salvas com sucesso
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
    <form action="{{ action('OwnerController@store') }}" method="POST" id="form_owner">
        {{ csrf_field() }}
        @if(isset($owner))
            <input type="hidden" name="owner_id" value="{{ $owner->id }}">
        @endif
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card mb-4">
                        <div class="card-header">Adicionar proprietário</div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Razão Social</label>
                                        <input type="text" name="owner_social_name" class="form-control" value="{{ isset($owner) ? $owner->social_name : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Nome Fantasia</label>
                                        <input type="text" name="owner_name" class="form-control" value="{{ isset($owner) ? $owner->name : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Alias/Identificador</label>
                                        <input type="text" name="owner_alias" class="form-control" value="{{ isset($owner) ? $owner->alias : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Documento</label>
                                        <input type="text" name="owner_document" class="form-control document_dyn" value="{{ isset($owner) ? $owner->document : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Telefone</label>
                                        <input type="text" name="owner_phone" class="form-control sp_celphones" value="{{ isset($owner) ? $owner->telefone : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>E-Mail</label>
                                        <input type="email" name="owner_email" class="form-control" value="{{ isset($owner) ? $owner->email : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Status</label>
                                        <select class="form-control req" name="owner_status" required>
                                            <option value="">Selecione...</option>
                                            <option value="ACTIVE" {{ isset($owner) && $owner->status == 'ACTIVE' ? 'selected' : '' }}>Ativo</option>
                                            <option value="INATIVE" {{ isset($owner) && $owner->status == 'INATIVE' ? 'selected' : '' }}>Inativo</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>CEP</label>
                                        <input type="text" name="owner_zipcode" class="form-control cep" value="{{ isset($owner) ? $owner->cep : '' }}" required id="cep">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Logradouro</label>
                                        <input type="text" name="owner_street" class="form-control auto_address" value="{{ isset($owner) ? $owner->logradouro : '' }}" required id="endereco">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>Nº</label>
                                        <input type="text" name="owner_number" class="form-control" value="{{ isset($owner) ? $owner->numero : '' }}" required maxlength="10">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <label>Complemento</label>
                                        <input type="text" name="owner_complement" class="form-control" value="{{ isset($owner) ? $owner->complemento : '' }}" required>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Bairro</label>
                                        <input type="text" name="owner_district" class="form-control auto_address" value="{{ isset($owner) ? $owner->bairro : '' }}" required id="bairro">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-5">
                                    <div class="form-group">
                                        <label>Cidade</label>
                                        <input type="text" name="owner_city" class="form-control auto_address" value="{{ isset($owner) ? $owner->cidade : '' }}" required id="cidade">
                                        <input type="hidden" name="owner_city_ibge" class="auto_address" value="{{ isset($owner) ? $owner->cidade_ibge : '' }}" required id="ibge">
                                    </div>
                                </div>
                                <div class="col-12 col-sm-2">
                                    <div class="form-group">
                                        <label>UF</label>
                                        <input type="text" name="owner_uf" class="form-control auto_address" value="{{ isset($owner) ? $owner->uf : '' }}" required id="uf">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between">Contas <button type="button" class="btn btn-link btn-sm pt-0" id="add_account">Adicionar conta</button></div>
                    </div>
                    <div id="accounts">
                        @if(isset($owner) && $owner->accounts->count())
                            @foreach($owner->accounts as $account)
                                @include('owners.old_account')
                            @endforeach
                        @endif
                    </div>
                    <div class="card-footer d-flex justify-content-end">
                        <button type="submit" class="btn btn-success">Salvar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <div id="accounts_aux" class="d-none">@include('owners.account')</div>
@endsection
@section('js')
    <script>
        $(document).ready(function() {
            $("#cep").keyup(function() {
                var cep = $(this).val().replace(/\D/g, '');
                if (cep != "") {
                    var validacep = /^[0-9]{8}$/;
                    if (cep.length == 8) {
                        $("#loading").fadeIn();
                        if(validacep.test(cep)) {
                            $(".auto_address").val("...");
                            $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                                console.log(dados);
                                if (!("erro" in dados)) {
                                    $("#endereco").val(dados.logradouro);
                                    $("#bairro").val(dados.bairro);
                                    $("#cidade").val(dados.localidade);
                                    $("#uf").val(dados.uf);
                                    $("#ibge").val(dados.ibge);
                                } else {
                                    $(".auto_address").val("");
                                }
                            });
                        } else {
                            $(".auto_address").val("");
                            alert("Formato de CEP inválido.");
                        }
                    } else {
                        $(".auto_address").val("");
                    }
                } else {
                    $(".auto_address").val("");
                }
            });

            $(document).on('click', '.remove_account', function() { $(this).closest('tr').remove(); });

            $(document).on('click', '#add_account', function() {
                var clone = $("#accounts_aux").clone().removeClass('d-none');
                $("#accounts").append($("<div>").append(clone).html());

                $(".req").each(function() { $(this).rules("add", { required: true }); });
            });

            $(document).on('change', '.reiniciar', function() {
                $(this).closest('.row').find('.numero_remessa').addClass('d-none');
                if($(this).val() == 0) $(this).closest('.row').find('.numero_remessa').removeClass('d-none');
            });

            $(document).on('change', '.banco', function() {
                $(this).closest('.row').find('.div_densidade').addClass('d-none');
                if($(this).val() == '033' && $(this).closest('.row').find('.cnab').val() == '240') $(this).closest('.row').find('.div_densidade').removeClass('d-none');
            });

            $(document).on('change', '.cnab', function() {
                $(this).closest('.row').find('.div_densidade').addClass('d-none');
                if($(this).val() == '240' && $(this).closest('.row').find('.banco').val() == '033') $(this).closest('.row').find('.div_densidade').removeClass('d-none');
            });

            $.validator.addClassRules("densidade_remessa", { required: function(element) { return $(element).closest('.row').find('.banco').val() == '033' && $(element).closest('.row').find('.cnab').val() == '240'; } });
            $.validator.addClassRules("remessa", { required: function(element) { return $(element).closest('.row').find('.reiniciar').val() == 0; } });
            $("#form_owner").validate({
                ignore: [],
                checkForm: function() {
                    this.prepareForm();
                    for ( var i = 0, elements = (this.currentElements = this.elements()); elements[i]; i++ ) {
                        if (this.findByName( elements[i].name ).length != undefined && this.findByName( elements[i].name ).length > 1) {
                            for (var cnt = 0; cnt < this.findByName( elements[i].name ).length; cnt++) {
                            this.check( this.findByName( elements[i].name )[cnt] );
                            }
                        } else {
                            this.check( elements[i] );
                        }
                    }
                    return this.valid();
                }
            });
        });
    </script>
@endsection