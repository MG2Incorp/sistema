@extends('layouts.app')
@section('css')
    <link href="{{ asset('date/datedropper.min.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .full-height { height: 50vh; }
        .flex-center { align-items: center; display: flex; justify-content: center; }
    </style>
@endsection
@section('content')
    <form action="{{ action('ProposalController@store') }}" method="POST" id="form_proposal">
        @csrf
        <input type="hidden" name="property_id" value="{{ $property->id }}">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h4 class="text-center mb-3">Formulário de Proposta</h4>
                </div>
                <div class="col-12">
                    <table class="table" style="font-size: 14px">
                        <thead>
                            <tr class="table-success text-center">
                                <th>Empreendimento</th>
                                <th width="10%">Bloco</th>
                                <th width="8%">Unidade</th>
                                <th width="8%">Entrega</th>
                                <th width="10%">Área</th>
                                <th width="10%">Vagas</th>
                                <th width="8%">Data</th>
                                <th>Valor</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-success text-center">
                                <td>{{ $property->block->building->project->name }}</td>
                                <td>{{ $property->block->building->name }}</td>
                                <td>{{ $property->number }}</td>
                                <td>{{ formatData($property->block->building->project->finish_at) }}</td>
                                <td>{{ $property->size }} m²</td>
                                <td>{{ $property->notes }}</td>
                                <td>{{ formatData(date('Y-m-d')) }}</td>
                                <td>R$ {{ formatMoney($property->value) }}</td>
                            </tr>
                        </tbody>
                    </table>
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        @for($i = 1; $i < 5; $i++)
                            <li class="nav-item {{ $i == 1 ? '' : 'd-none' }}" id="nav{{ $i }}">
                                <a class="nav-link {{ $i == 1 ? 'active' : '' }}" data-toggle="tab" href="#propo{{ $i }}" role="tab" aria-controls="propo{{ $i }}" aria-selected="true">Proponente {{ $i }}</a>
                                <input type="checkbox" class="check_propo d-none" name="check_propo[]" value="{{ $i }}" id="check_propo{{ $i }}" {{ $i == 1 ? 'checked' : '' }}>
                            </li>
                        @endfor
                        <li class="nav-item"><a class="nav-link pointer" id="add_propo"><i class="fas fa-plus"></i></a></li>
                        <li class="nav-item"><a class="nav-link d-none pointer" id="remove_propo"><i class="fas fa-times"></i></a></li>
                    </ul>
                    <div class="tab-content">
                        @for($i = 1; $i < 5; $i++)
                            <div class="tab-pane {{ $i == 1 ? 'active' : '' }}" id="propo{{ $i }}" role="tabpanel">
                                <div class="card border-top-0 border-bottom-0">
                                    <div class="card-body">
                                        <div class="row justify-content-between">
                                            <div class="col">
                                                <h5 class="m-0 p-0">Dados do Proponente ({{ $i }})</h5>
                                            </div>
                                            <div class="col" align="right">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="radio" name="main" value="{{ $i }}" {{ $i == 1 ? 'checked' : '' }}>
                                                    <label class="form-check-label">Comprador principal</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 col-sm-3">
                                                <label>Tipo de pessoa</label>
                                                <div class="form-group">
                                                    <select name="type[{{ $i }}]" class="form-control type" data-id="{{ $i }}" id="type{{ $i }}">
                                                        <option value="Física">Física</option>
                                                        <option value="Jurídica">Jurídica</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>CPF/CNPJ</label>
                                                    <input type="text" name="document[{{ $i }}]" class="form-control document doc" data-id="{{ $i }}" data-type="PROPO">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>RG</label>
                                                    <div class="input-group">
                                                        <input type="text" name="rg[{{ $i }}]" class="form-control w-25 rg" placeholder="Número" data-id="{{ $i }}">
                                                        <input type="text" name="emitter[{{ $i }}]" class="form-control emitter" placeholder="Emissor" value="SSP" data-id="{{ $i }}">
                                                        <select name="rg_state[{{ $i }}]" id="" class="form-control rg_uf" data-id="{{ $i }}">
                                                            @foreach(getStates() as $state)
                                                                <option value="{{ $state }}" {{ $state == 'SP' ? 'selected' : '' }}>{{ $state }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>Proporção</label>
                                                    <div class="input-group">
                                                        <input type="text" name="proportion[{{ $i }}]" class="form-control money proporcao proportion" value="0,00" data-id="{{ $i }}">
                                                        <span class="input-group-append"><span class="input-group-text">%</span></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-7">
                                                <div class="form-group">
                                                    <label>Nome</label>
                                                    <input type="text" name="name[{{ $i }}]" class="form-control name" data-id="{{ $i }}" id="NAME_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-5">
                                                <div class="form-group">
                                                    <label>E-Mail</label>
                                                    <input type="email" name="email[{{ $i }}]" class="form-control email" data-id="{{ $i }}" id="EMAIL_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>Sexo</label>
                                                    <select name="gender[{{ $i }}]" class="form-control gender" data-id="{{ $i }}" id="GENDER_PROPO{{ $i }}">
                                                        <option value="">Selecione...</option>
                                                        <option value="Masculino">Masculino</option>
                                                        <option value="Feminino">Feminino</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>Nascimento</label>
                                                    <input type="date" name="birthdate[{{ $i }}]" class="form-control data birthdate" data-id="{{ $i }}" id="BIRTHDATE_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>Telefone</label>
                                                    <input type="text" name="phone[{{ $i }}]" class="form-control telefone" id="TELEPHONE_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>Celular</label>
                                                    <input type="text" name="cellphone[{{ $i }}]" class="form-control celular cellphone" data-id="{{ $i }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Nome da mãe</label>
                                                    <input type="text" name="mother_name[{{ $i }}]" class="form-control mother_name" data-id="{{ $i }}" id="MOTHER_NAME_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Nome do pai</label>
                                                    <input type="text" name="father_name[{{ $i }}]" class="form-control father_name" data-id="{{ $i }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-5">
                                                <div class="form-group">
                                                    <label>Naturalidade</label>
                                                    <input type="text" name="birthplace[{{ $i }}]" class="form-control birthplace" data-id="{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>País</label>
                                                    <input type="text" name="country[{{ $i }}]" class="form-control country" data-id="{{ $i }}" value="Brasil">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>Moradia atual</label>
                                                    <select name="house[{{ $i }}]" class="form-control house" data-id="{{ $i }}">
                                                        <option value="">Selecione...</option>
                                                        <option value="Própria">Própria</option>
                                                        <option value="Alugada">Alugada</option>
                                                        <option value="Cedida">Cedida</option>
                                                        <option value="Ocupada">Ocupada</option>
                                                        <option value="Financiada">Financiada</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>Renda bruta</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                                        <input type="text" class="form-control money gross_income" name="gross_income[{{ $i }}]" value="0,00" data-id="{{ $i }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>Renda líquida</label>
                                                    <div class="input-group">
                                                        <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                                        <input type="text" class="form-control money net_income" name="net_income[{{ $i }}]" value="0,00" data-id="{{ $i }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Profissão</label>
                                                    <input type="text" name="occupation[{{ $i }}]" class="form-control occupation" data-id="{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Cartório</label>
                                                    <input type="text" name="registry[{{ $i }}]" class="form-control">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Estado civil</label>
                                                    <select name="civil_status[{{ $i }}]" class="form-control estado_civil civil_status" id="estado_civil{{ $i }}" data-id="{{ $i }}">
                                                        <option value="">Selecione...</option>
                                                        <option value="Solteiro">Solteiro</option>
                                                        <option value="Casado">Casado</option>
                                                        <option value="Divorciado">Divorciado</option>
                                                        <option value="Víuvo">Víuvo</option>
                                                        <option value="Amasiado">Amasiado</option>
                                                        <option value="Namorando">Namorando</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-8" id="div_regime_casamento{{ $i }}" style="display: none">
                                                <div class="row">
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label>Regime de casamento</label>
                                                            <select name="marriage[{{ $i }}]" class="form-control">
                                                                <option value="">Selecione...</option>
                                                                <option value="Comunhão parcial de bens">Comunhão parcial de bens</option>
                                                                <option value="Comunhão universal de bens">Comunhão universal de bens</option>
                                                                <option value="Separação total de bens">Separação total de bens</option>
                                                                <option value="Participação final nos aquestos">Participação final nos aquestos</option>
                                                            </select>
                                                        </div>
                                                    </div>
                                                    <div class="col-12 col-sm-6">
                                                        <div class="form-group">
                                                            <label>Dados do cônjuge</label>
                                                            <button type="button" class="btn btn-info btn-block" data-toggle="modal" data-target="#modal_propo{{ $i }}">Ver/Inserir dados do cônjuge</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-between">
                                            <div class="col">
                                                <h5 class="m-0 p-0">Dados Profissionais do Proponente ({{ $i }})</h5>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 col-sm-6">
                                                <div class="form-group">
                                                    <label>Empresa</label>
                                                    <input type="text" name="company[{{ $i }}]" class="form-control company" data-id="{{ $i }}" id="NAME_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>CNPJ</label>
                                                    <input type="text" name="company_document[{{ $i }}]" class="form-control cnpj company_cnpj" data-id="{{ $i }}" data-type="COMPANY">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-3">
                                                <div class="form-group">
                                                    <label>Cargo</label>
                                                    <input type="text" name="role[{{ $i }}]" class="form-control role" data-id="{{ $i }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Admissão</label>
                                                    <input type="date" name="hired_at[{{ $i }}]" class="form-control data hire" data-id="{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Telefone</label>
                                                    <input type="text" name="company_phone[{{ $i }}]" class="form-control telefone company_phone" data-id="{{ $i }}" id="TELEPHONE_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Celular</label>
                                                    <input type="text" name="company_cellphone[{{ $i }}]" class="form-control celular">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>CEP</label>
                                                    <input data-which="COMPANY" data-prop="{{ $i }}" type="text" name="company_zipcode[{{ $i }}]" class="form-control cep" data-custom-id="ZIPCODE_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Logradouro</label>
                                                    <input type="text" name="company_street[{{ $i }}]" class="form-control" id="rua_empresa{{ $i }}" data-custom-id="STREET_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>Número</label>
                                                    <input type="text" name="company_number[{{ $i }}]" class="form-control" data-custom-id="NUMBER_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Complemento</label>
                                                    <input type="text" name="company_complement[{{ $i }}]" class="form-control" data-custom-id="COMPLEMENT_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-5">
                                                <div class="form-group">
                                                    <label>Bairro</label>
                                                    <input type="text" name="company_district[{{ $i }}]" class="form-control" id="bairro_empresa{{ $i }}" data-custom-id="DISTRICT_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-5">
                                                <div class="form-group">
                                                    <label>Cidade</label>
                                                    <input type="text" name="company_city[{{ $i }}]" class="form-control" id="cidade_empresa{{ $i }}" data-custom-id="CITY_COMPANY{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>Estado</label>
                                                    <select name="company_state[{{ $i }}]" class="form-control" id="uf_empresa{{ $i }}" data-custom-id="STATE_COMPANY{{ $i }}">
                                                        <option value="">Selecione...</option>
                                                        @foreach(getStates() as $state)
                                                            <option value="{{ $state }}">{{ $state }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row justify-content-between">
                                            <div class="col">
                                                <h5 class="m-0 p-0">Endereço Residencial do Proponente ({{ $i }})</h5>
                                            </div>
                                            <div class="col" align="right">
                                                <div class="form-check form-check-inline">
                                                    <input class="form-check-input" type="checkbox" name="billing_address[{{ $i }}]" value="1">
                                                    <label class="form-check-label">Mesmo endereço de cobrança</label>
                                                </div>
                                            </div>
                                        </div>
                                        <hr>
                                        <div class="row">
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>CEP</label>
                                                    <input data-which="HOUSE" data-prop="{{ $i }}" type="text" name="zipcode[{{ $i }}]" class="form-control cep zipcode" data-id="{{ $i }}" data-custom-id="ZIPCODE_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Logradouro</label>
                                                    <input type="text" name="street[{{ $i }}]" class="form-control street" id="rua_casa{{ $i }}" data-id="{{ $i }}" data-custom-id="STREET_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>Número</label>
                                                    <input type="text" name="number[{{ $i }}]" class="form-control number" data-id="{{ $i }}" data-custom-id="NUMBER_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-4">
                                                <div class="form-group">
                                                    <label>Complemento</label>
                                                    <input type="text" name="complement[{{ $i }}]" class="form-control complement" data-id="{{ $i }}" data-custom-id="COMPLEMENT_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-12 col-sm-5">
                                                <div class="form-group">
                                                    <label>Bairro</label>
                                                    <input type="text" name="district[{{ $i }}]" class="form-control district" id="bairro_casa{{ $i }}" data-id="{{ $i }}" data-custom-id="DISTRICT_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-5">
                                                <div class="form-group">
                                                    <label>Cidade</label>
                                                    <input type="text" name="city[{{ $i }}]" class="form-control city" id="cidade_casa{{ $i }}" data-id="{{ $i }}" data-custom-id="CITY_PROPO{{ $i }}">
                                                </div>
                                            </div>
                                            <div class="col-12 col-sm-2">
                                                <div class="form-group">
                                                    <label>Estado</label>
                                                    <select name="state[{{ $i }}]" class="form-control state" id="uf_casa{{ $i }}" data-id="{{ $i }}" data-custom-id="STATE_PROPO{{ $i }}">
                                                        <option value="">Selecione...</option>
                                                        @foreach(getStates() as $state)
                                                            <option value="{{ $state }}">{{ $state }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="modal fade bd-example-modal-xl" id="modal_propo{{ $i }}" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
                                <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="row justify-content-between">
                                                <div class="col">
                                                    <h5 class="m-0 p-0">Dados do Conjûge do Proponente {{ $i }}</h5>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>CPF</label>
                                                        <input type="text" name="spouse_document[{{ $i }}]" class="form-control spouse_document cpf" data-type="SPOUSE" data-id="{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>RG</label>
                                                        <div class="input-group">
                                                            <input type="text" name="spouse_rg[{{ $i }}]" class="form-control w-25 rg" placeholder="Número">
                                                            <input type="text" name="spouse_emitter[{{ $i }}]" class="form-control" placeholder="Emissor" value="SSP">
                                                            <select name="spouse_rg_state[{{ $i }}]" id="" class="form-control">
                                                                @foreach(getStates() as $state)
                                                                    <option value="{{ $state }}" {{ $state == 'SP' ? 'selected' : '' }}>{{ $state }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Proporção</label>
                                                        <div class="input-group">
                                                            <input type="text" name="spouse_proportion[{{ $i }}]" class="form-control money spouse_proporcao spouse_proportion" value="0,00" data-id="{{ $i }}">
                                                            <span class="input-group-append"><span class="input-group-text">%</span></span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-7">
                                                    <div class="form-group">
                                                        <label>Nome</label>
                                                        <input type="text" name="spouse_name[{{ $i }}]" class="form-control" id="NAME_SPOUSE{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-5">
                                                    <div class="form-group">
                                                        <label>E-Mail</label>
                                                        <input type="email" name="spouse_email[{{ $i }}]" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Sexo</label>
                                                        <select name="spouse_gender[{{ $i }}]" class="form-control" id="GENDER_SPOUSE{{ $i }}">
                                                            <option value="">Selecione...</option>
                                                            <option value="Masculino">Masculino</option>
                                                            <option value="Feminino">Feminino</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Nascimento</label>
                                                        <input type="date" name="spouse_birthdate[{{ $i }}]" class="form-control data" id="BIRTHDATE_SPOUSE{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Telefone</label>
                                                        <input type="text" name="spouse_phone[{{ $i }}]" class="form-control telefone">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Celular</label>
                                                        <input type="text" name="spouse_cellphone[{{ $i }}]" class="form-control celular">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label>Nome da mãe</label>
                                                        <input type="text" name="spouse_mother_name[{{ $i }}]" class="form-control" id="MOTHER_NAME_SPOUSE{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label>Nome do pai</label>
                                                        <input type="text" name="spouse_father_name[{{ $i }}]" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label>Naturalidade</label>
                                                        <input type="text" name="spouse_birthplace[{{ $i }}]" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label>País</label>
                                                        <input type="text" name="spouse_country[{{ $i }}]" class="form-control" value="Brasil">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Renda bruta</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                                            <input type="text" class="form-control money" name="spouse_gross_income[{{ $i }}]" value="0,00">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Renda líquida</label>
                                                        <div class="input-group">
                                                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                                            <input type="text" class="form-control money" name="spouse_net_income[{{ $i }}]" value="0,00">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Profissão</label>
                                                        <input type="text" name="spouse_occupation[{{ $i }}]" class="form-control">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Cartório</label>
                                                        <input type="text" name="spouse_registry[{{ $i }}]" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row justify-content-between">
                                                <div class="col">
                                                    <h5 class="m-0 p-0">Dados da Empresa do Cônjuge do Proponente {{ $i }}</h5>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label>Empresa</label>
                                                        <input type="text" name="spouse_company[{{ $i }}]" class="form-control" data-id="{{ $i }}" id="NAME_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>CNPJ</label>
                                                        <input type="text" name="spouse_company_document[{{ $i }}]" class="form-control cnpj company_cnpj" data-id="{{ $i }}" data-type="SPOUSE_COMPANY">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Cargo</label>
                                                        <input type="text" name="spouse_role[{{ $i }}]" class="form-control">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Admissão</label>
                                                        <input type="date" name="spouse_hired_at[{{ $i }}]" class="form-control data">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Telefone</label>
                                                        <input type="text" name="spouse_company_phone[{{ $i }}]" class="form-control telefone" id="TELEPHONE_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Celular</label>
                                                        <input type="text" name="spouse_company_cellphone[{{ $i }}]" class="form-control celular">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>CEP</label>
                                                        <input data-which="SPOUSE" data-prop="{{ $i }}" type="text" name="spouse_company_zipcode[{{ $i }}]" class="form-control cep" data-custom-id="ZIPCODE_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Logradouro</label>
                                                        <input type="text" name="spouse_company_street[{{ $i }}]" class="form-control" id="rua_conjuge{{ $i }}" data-custom-id="STREET_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Número</label>
                                                        <input type="text" name="spouse_company_number[{{ $i }}]" class="form-control" data-custom-id="NUMBER_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Complemento</label>
                                                        <input type="text" name="spouse_company_complement[{{ $i }}]" class="form-control" data-custom-id="COMPLEMENT_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-5">
                                                    <div class="form-group">
                                                        <label>Bairro</label>
                                                        <input type="text" name="spouse_company_district[{{ $i }}]" class="form-control" id="bairro_conjuge{{ $i }}" data-custom-id="DISTRICT_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-5">
                                                    <div class="form-group">
                                                        <label>Cidade</label>
                                                        <input type="text" name="spouse_company_city[{{ $i }}]" class="form-control" id="cidade_conjuge{{ $i }}" data-custom-id="CITY_SPOUSE_COMPANY{{ $i }}">
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Estado</label>
                                                        <select name="spouse_company_state[{{ $i }}]" class="form-control" id="uf_conjuge{{ $i }}" data-custom-id="STATE_SPOUSE_COMPANY{{ $i }}">
                                                            <option value="">Selecione...</option>
                                                            @foreach(getStates() as $state)
                                                                <option value="{{ $state }}">{{ $state }}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endfor
                    </div>
                    <div class="card border-top-0">
                        <div class="card-body">
                            <div class="row justify-content-between">
                                <div class="col">
                                    <h5 class="m-0 p-0">Informações sobre a Proposta</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Mídia</label>
                                        <select name="media" class="form-control media">
                                            <option value="">Selecione...</option>
                                            <option value="E-mail Marketing">E-mail Marketing</option>
                                            <option value="Facebook">Facebook</option>
                                            <option value="Google">Google</option>
                                            <option value="Instagram">Instagram</option>
                                            <option value="Mercado Livre">Mercado Livre</option>
                                            <option value="Site">Site</option>
                                            <option value="Viva Real">Viva Real</option>
                                            <option value="WhatsApp">WhatsApp</option>
                                            <option value="Zap Imóveis">Zap Imóveis</option>
                                            <option value="Captação">Captação</option>
                                            <option value="Indicação">Indicação</option>
                                            <option value="Plantão">Plantão</option>
                                            <option value="Telefone">Telefone</option>
                                            <option value="TV">TV</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12 col-sm-6">
                                    <div class="form-group">
                                        <label>Motivo da compra</label>
                                        <select name="reason" class="form-control reason">
                                            <option value="">Selecione...</option>
                                            <option value="Investimento">Investimento</option>
                                            <option value="Moradia">Moradia</option>
                                            <option value="Aluguel">Aluguel</option>
                                            <option value="Segurança">Segurança</option>
                                            <option value="Outro">Outro</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 col-sm-12">
                                    <div class="form-group">
                                        <label>Observações</label>
                                        <textarea name="notes" class="form-control" rows="3"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col">
                                    <h5 class="m-0 p-0">Condições de Pagamento</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-4">
                                    <div class="form-group">
                                        <label>Contrato</label>
                                        <select name="modality" class="form-control">
                                            <option value="">Selecione...</option>
                                            @foreach($contracts as $contract)
                                                <option value="{{ $contract->id }}">{{ $contract->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <button type="button" class="btn btn-info btn-sm mb-2" id="add_parcela"><i class="fas fa-plus"></i> Adicionar pagamento</button>
                                            <table class="table table-bordered table-sm m-0">
                                                <thead>
                                                    <tr>
                                                        <th width="5%"></th>
                                                        <th width="18%">Componentes</th>
                                                        <th>Método</th>
                                                        <th width="8%">Qtd</th>
                                                        <th width="20%">Vencimento</th>
                                                        <th width="18%">Valor Unitário</th>
                                                        <th width="8%" class="d-none">%</th>
                                                        <th>Valor Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody id="tabela_parcelas">
                                                    <tr class="linha_parcela" data-line="0">
                                                        <td></td>
                                                        <td>Entrada/Sinal <input type="hidden" name="pay_componentes[0]" value="Entrada/Sinal" class="pay_componentes"></td>
                                                        <td class="form-group m-0">
                                                            <select name="pay_metodos[0]" class="form-control pay_metodos">
                                                                <option value="">Selecione...</option>
                                                                <option value="Dinheiro">Dinheiro</option>
                                                                <option value="Cheque">Cheque</option>
                                                                <option value="Boleto">Boleto</option>
                                                                <option value="Cartão de Débito">Cartão de Débito</option>
                                                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                                                <option value="Financiamento Bancário">Financiamento Bancário</option>
                                                                <option value="Nota promissória">Nota promissória</option>
                                                                <option value="Cheque+Boleto">Cheque+Boleto</option>
                                                                <option value="Transferência Bancária">Transferência Bancária</option>
                                                                <option value="Comissão">Comissão</option>
                                                                <option value="TED/DOC">TED/DOC</option>
                                                            </select>
                                                        </td>
                                                        <td class="form-group m-0"><input type="text" name="pay_quantidades[0]" class="form-control quantidade pay_quantidades" value="1"></td>
                                                        <td class="form-group m-0"><input type="date" name="pay_validades[0]" class="form-control data pay_validades"></td>
                                                        <td class="form-group m-0">
                                                            <div class="input-group">
                                                                <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                                                <input type="text" class="form-control money valor_unitario pay_valores" name="pay_valores[0]" value="0,00">
                                                            </div>
                                                        </td>
                                                        <td class="porcentagem_linha d-none"></td>
                                                        <td class="valor_total_linha"></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                        <div class="card-footer">
                                            <label>Índice de Correção Monetária</label>
                                            <div class="row">
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group m-0">
                                                        <select name="correction_type" class="form-control" id="tem_correcao">
                                                            <option value="0">Não</option>
                                                            <option value="Mensal">Sim, mensal.</option>
                                                            <option value="Bimestral">Sim, bimestral.</option>
                                                            <option value="Trimestral">Sim, trimestral.</option>
                                                            <option value="Semestral">Sim, semestral.</option>
                                                            <option value="Anual">Sim, anual.</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-9 d-none" id="div_tem_correcao">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-7">
                                                            <div class="form-group m-0">
                                                                <select name="correction_index" class="form-control" id="indice_correcao">
                                                                    <option value="">Selecione o índice...</option>
                                                                    @foreach($indexes as $index)
                                                                        <option value="{{ $index->id }}">{{ $index->name }}</option>
                                                                    @endforeach
                                                                    <option value="Outro">Outro</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-5 d-none" id="div_outro_indice">
                                                            <div class="form-group m-0">
                                                                <input type="text" name="other_correction_type" class="form-control">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col">
                                    <h5 class="m-0 p-0">Aplicação de Desconto</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12 col-sm-3">
                                    <div class="form-group">
                                        <div class="input-group">
                                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>
                                            <input type="text" class="form-control money" name="discount" value="0,00">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-between">
                                <div class="col">
                                    <h5 class="m-0 p-0">Avaliação da Proposta</h5>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr class="text-center">
                                                <th>Valor da Unidade</th>
                                                <th>Acréscimo</th>
                                                <th>Valor da Proposta</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td align="center">R$ {{ formatMoney($property->value) }} <input type="hidden" id="input_valor_unidade" value="{{ $property->value }}"></td>
                                                <td align="center" id="acrescimo"></td>
                                                <td align="center" id="valor_da_proposta"></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            @if(isset($users))
                                <div class="row justify-content-between">
                                    <div class="col">
                                        <h5 class="m-0 p-0">Responsável</h5>
                                        <hr>
                                        <select name="user_id" class="form-control">
                                            <option value="">Selecione...</option>
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            @else
                                <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                            @endif
                            <hr>
                            <div class="row justify-content-end">
                                <div class="col-12 col-sm-4">
                                    <button type="submit" class="btn btn-success btn-block">Salvar proposta</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <!-- <div class="vh-100 d-flex justify-content-center align-items-center" id="loading" style="display: none !important"><img src="{{ asset('img/loading.gif') }}" width="100px"></div> -->
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('date/datedropper.min.js') }}"></script>
    <script>
        function cpf(strCPF) {
            var Soma;
            var Resto;
            Soma = 0;
            if (strCPF == "00000000000") return false;

            for (i=1; i<=9; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (11 - i);
            Resto = (Soma * 10) % 11;

            if ((Resto == 10) || (Resto == 11))  Resto = 0;
            if (Resto != parseInt(strCPF.substring(9, 10)) ) return false;

            Soma = 0;
            for (i = 1; i <= 10; i++) Soma = Soma + parseInt(strCPF.substring(i-1, i)) * (12 - i);
            Resto = (Soma * 10) % 11;

            if ((Resto == 10) || (Resto == 11))  Resto = 0;
            if (Resto != parseInt(strCPF.substring(10, 11) ) ) return false;
            return true;
        }

        function cnpj(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g,'');

            if(cnpj == '') return false;

            if (cnpj.length != 14)
                return false;

            if (cnpj == "00000000000000" || cnpj == "11111111111111" || cnpj == "22222222222222" || cnpj == "33333333333333" || cnpj == "44444444444444" || cnpj == "55555555555555" ||
                cnpj == "66666666666666" ||	cnpj == "77777777777777" ||	cnpj == "88888888888888" ||	cnpj == "99999999999999")
                return false;

            tamanho = cnpj.length - 2
            numeros = cnpj.substring(0,tamanho);
            digitos = cnpj.substring(tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(0))
                return false;

            tamanho = tamanho + 1;
            numeros = cnpj.substring(0,tamanho);
            soma = 0;
            pos = tamanho - 7;
            for (i = tamanho; i >= 1; i--) {
            soma += numeros.charAt(tamanho - i) * pos--;
            if (pos < 2)
                    pos = 9;
            }
            resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
            if (resultado != digitos.charAt(1))
                return false;

            return true;
        }

        function limpa_formulário_cep(type, propo) {
            $("#rua_"+type+propo).val("");
            $("#bairro_"+type+propo).val("");
            $("#cidade_"+type+propo).val("");
            $("#uf_"+type+propo).val("");
        }

        function currencyFormatted(value, str_cifrao) {
            return str_cifrao + ' ' + value.formatMoney(2, ',', '.');
        }

        Number.prototype.formatMoney = function (c, d, t) {
            var n = this, c = isNaN(c = Math.abs(c)) ? 2 : c, d = d == undefined ? "." : d, t = t == undefined ? "," : t, s = n < 0 ? "-" : "", i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;
            return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
        };

        function proportions() {
            var soma = parseInt(0);
            var empty = false;

            $(".proporcao").each(function() {
                var propo = $(this).attr('data-id');
                if ($("#check_propo"+propo).is(':checked')) {
                    if($.trim($(this).val()).length > 0) {
                        soma = parseInt(soma) + parseInt($(this).val().replace(/\./g, '').replace(',', '.'));
                    } else {
                        empty = true;
                    }
                }
            });

            $(".spouse_proporcao").each(function() {
                var propo = $(this).attr('data-id');
                if ($("#check_propo"+propo).is(':checked') && $("#estado_civil"+propo).val() == 'Casado') {
                    if($.trim($(this).val()).length > 0) {
                        soma = parseInt(soma) + parseInt($(this).val().replace(/\./g, '').replace(',', '.'));
                    } else {
                        empty = true;
                    }
                }
            });

            if (!empty && (soma > 100 || soma < 100)) return 0;

            return 1;
        }

        function calcula_parcela() {
            var soma = 0;
            var linhas = 0;

            $(".linha_parcela").each(function() {
                var qtd = $(this).find('.quantidade').val();
                var valor = $(this).find('.valor_unitario').val().replace(/\./g, '').replace(',', '.');
                var soma_linha = parseFloat(valor)*qtd;
                soma += soma_linha*100;

                $(this).find('.valor_total_linha').text(currencyFormatted(soma_linha, 'R$'));

                linhas = parseInt(linhas) + 1;
            });

            var aux = soma;
            var soma_final = soma/100;

            $("#soma_total_parcelas").text(currencyFormatted(soma_final, 'R$'));
            $("#valor_da_proposta").text(currencyFormatted(soma_final, 'R$'));

            $(".linha_parcela").each(function() {
                var qtd = $(this).find('.quantidade').val();
                var valor = $(this).find('.valor_unitario').val().replace(/\./g, '').replace(',', '.');

                if (soma_final == 0) {
                    var porcentagem = 0;
                } else {
                    var porcentagem = (parseFloat(valor)*qtd*100)/soma_final;
                }

                $(this).find('.porcentagem_linha').text((Math.round(porcentagem*100)/100)+" %");
            });

            var valor_unidade = parseFloat($("#input_valor_unidade").val())*100;
            var diferenca = (soma-valor_unidade)/100;

            var porcentagem = ((diferenca*100)/valor_unidade)*100;

            $("#acrescimo").text(currencyFormatted(diferenca, 'R$')+' ('+porcentagem+' %)');
        }

        $(document).ready(function(){
            // $(".data").dateDropper();

            calcula_parcela();

            $(document).on('blur', '.proporcao', function() { var ret = proportions(); });
            $(document).on('blur', '.spouse_proporcao', function() { var ret = proportions(); });

            $(document).on('change', '#tem_correcao', function(){
                if ($(this).val() == '0') {
                    $("#div_tem_correcao").removeClass('d-block').addClass('d-none');
                } else {
                    $("#div_tem_correcao").addClass('d-block').removeClass('d-none');
                    if ($('#indice_correcao').val() == 'Outro') {
                        $("#div_outro_indice").addClass('d-block').removeClass('d-none');
                    } else {
                        $("#div_outro_indice").removeClass('d-block').addClass('d-none');
                    }
                }
            });

            $(document).on('change', '#indice_correcao', function(){
                if ($(this).val() == 'Outro') {
                    $("#div_outro_indice").addClass('d-block').removeClass('d-none');
                } else {
                    $("#div_outro_indice").removeClass('d-block').addClass('d-none');
                }
            });

            $(document).on('click', '#add_propo', function(){
                // var propo =
                $(".check_propo").each(function() {
                    if(!$(this).is(':checked')) {
                        $(this).prop('checked', true);
                        $("#nav"+$(this).val()).toggleClass('d-none').toggleClass('d-block');

                        if ($(this).val() > 1) $("#remove_propo").removeClass('d-none').addClass('d-block');

                        if ($(this).val() == 4) $("#add_propo").removeClass('d-block').addClass('d-none');

                        return false;
                    }
                });
            });

            $(document).on('click', '#remove_propo', function(){
                // var propo =
                $($(".check_propo").get().reverse()).each(function() {
                    if($(this).is(':checked')) {
                        $(this).prop('checked', false);
                        $("#nav"+$(this).val()).toggleClass('d-none').toggleClass('d-block');

                        // console.log($(this).val());

                        if ($(this).val() == 2) $("#remove_propo").removeClass('d-block').addClass('d-none');

                        if ($(this).val() < 5) $("#add_propo").removeClass('d-none').addClass('d-block');

                        return false;
                    }
                });
            });

            $(document).on('click', '#add_parcela', function() {
                var next = $(".linha_parcela").last().data('line') + 1;
                $("#tabela_parcelas").append(   '<tr class="linha_parcela" data-line="'+next+'">\
                                                    <td><button type="button" class="btn btn-danger btn-sm remove_parcela"><i class="fas fa-times"></i></button></td>\
                                                    <td class="form-group m-0">\
                                                        <select name="pay_componentes['+next+']" class="form-control pay_componentes">\
                                                            <option value="">Selecione...</option>\
                                                            <option value="Anual">Anual</option>\
                                                            <option value="Semestral">Semestral</option>\
                                                            <option value="Trimestral">Trimestral</option>\
                                                            <option value="Bimestral">Bimestral</option>\
                                                            <option value="Mensal">Mensal</option>\
                                                            <option value="Entrada/Sinal">Entrada/Sinal</option>\
                                                        </select>\
                                                    </td>\
                                                    <td class="form-group m-0">\
                                                        <select name="pay_metodos['+next+']" class="form-control pay_metodos">\
                                                            <option value="">Selecione...</option>\
                                                            <option value="Dinheiro">Dinheiro</option>\
                                                            <option value="Cheque">Cheque</option>\
                                                            <option value="Boleto">Boleto</option>\
                                                            <option value="Cartão de Débito">Cartão de Débito</option>\
                                                            <option value="Cartão de Crédito">Cartão de Crédito</option>\
                                                            <option value="Financiamento Bancário">Financiamento Bancário</option>\
                                                            <option value="Nota promissória">Nota promissória</option>\
                                                            <option value="Cheque+Boleto">Cheque+Boleto</option>\
                                                            <option value="Transferência Bancária">Transferência Bancária</option>\
                                                            <option value="Comissão">Comissão</option>\
                                                            <option value="TED/DOC">TED/DOC</option>\
                                                        </select>\
                                                    </td>\
                                                    <td class="form-group m-0"><input type="text" name="pay_quantidades['+next+']" class="form-control quantidade pay_quantidades"></td>\
                                                    <td class="form-group m-0"><input type="date" name="pay_validades['+next+']" class="form-control data pay_validades"></td>\
                                                    <td class="form-group m-0">\
                                                        <div class="input-group">\
                                                            <div class="input-group-prepend"><span class="input-group-text">R$</span></div>\
                                                            <input type="text" class="form-control money valor_unitario pay_valores" name="pay_valores['+next+']" value="0,00">\
                                                        </div>\
                                                    </td>\
                                                    <td class="porcentagem_linha d-none"></td>\
                                                    <td class="valor_total_linha"></td>\
                                                </tr>');
                $('.money').maskMoney({ thousands: '.', decimal: ',', allowZero: true });
                // $(".data").dateDropper();
            });

            $(document).on('blur', '.valor_unitario', function() { calcula_parcela(); });

            $(document).on('click', '.remove_parcela', function() { $(this).parent().parent().remove(); calcula_parcela(); });

            var options = {
                onKeyPress: function (cpf, ev, el, op) {
                    var masks = ['000.000.000-000', '00.000.000/0000-00'];
                    $('.document').mask((cpf.length > 14) ? masks[1] : masks[0], op);
                }
            }

            $('.document').length > 11 ? $('.document').mask('00.000.000/0000-00', options) : $('.document').mask('000.000.000-00#', options);

            $(".cep").blur(function() {
                var cep = $(this).val().replace(/\D/g, '');
                var tipo = $(this).attr('data-which');
                var propo = $(this).attr('data-prop');

                switch (tipo) {
                    case 'HOUSE': var type = 'casa'; break;
                    case 'COMPANY': var type = 'empresa'; break;
                    case 'SPOUSE': var type = 'conjuge'; break;
                }

                if (cep != "") {
                    var validacep = /^[0-9]{8}$/;
                    if(validacep.test(cep)) {

                        $("#rua_"+type+propo).val("...");
                        $("#bairro_"+type+propo).val("...");
                        $("#cidade_"+type+propo).val("...");
                        $("#uf_"+type+propo).val("...");

                        $.getJSON("https://viacep.com.br/ws/"+ cep +"/json/?callback=?", function(dados) {
                            if (!("erro" in dados)) {
                                $("#rua_"+type+propo).val(dados.logradouro);
                                $("#bairro_"+type+propo).val(dados.bairro);
                                $("#cidade_"+type+propo).val(dados.localidade);
                                $("#uf_"+type+propo).val(dados.uf);
                            } else {
                                limpa_formulário_cep(type, propo);
                                alert("CEP não encontrado.");
                            }
                        });
                    } else {
                        limpa_formulário_cep(type, propo);
                        alert("Formato de CEP inválido.");
                    }
                } else {
                    limpa_formulário_cep(type, propo);
                }
            });

            $(document).on('change', '.estado_civil', function(){
                var id = $(this).attr('id').substring(12);
                if ($(this).val() == 'Casado') {
                    $("#div_regime_casamento"+id).show();
                } else {
                    $("#div_regime_casamento"+id).hide();
                }
            });

            $(document).on('blur', '.document, .spouse_document, .company_cnpj', function() {
                return;
                
                var tipo = $(this).attr('data-type');
                var id = $(this).attr('data-id');

                if(!$(this).val().trim().length) {
                    $("#NAME_"+tipo+id).val('');
                    $("#GENDER_"+tipo+id).val('');
                    $("#BIRTHDATE_"+tipo+id).val('');
                    $("#MOTHER_NAME_"+tipo+id).val('');
                    $("#NAME_"+tipo+id).val('');
                    $("#EMAIL_"+tipo+id).val('');
                    $("input[data-custom-id='ZIPCODE_"+tipo+id+"']").val('');
                    $("select[data-custom-id='STATE_"+tipo+id+"']").val('');
                    $("input[data-custom-id='CITY_"+tipo+id+"']").val('');
                    $("input[data-custom-id='DISTRICT_"+tipo+id+"']").val('');
                    $("input[data-custom-id='STREET_"+tipo+id+"']").val('');
                    $("input[data-custom-id='NUMBER_"+tipo+id+"']").val('');
                    $("input[data-custom-id='COMPLEMENT_"+tipo+id+"']").val('');
                    $("#TELEPHONE_"+tipo+id).val('');

                    return;
                }

                $("#form_proposal").hide();
                $("#loading").fadeIn();

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    url: "{{ URL::route('proposals.search') }}", type: "POST", dataType: 'json', data: { doc: $(this).val() }, cache: false, processData:true,
                    success: function(data) {

                        if (data.error) {
                            alert('Não foi possível encontrar informações sobre o documento informado. Por favor, verifique o dado informado.');
                            $("#NAME_"+tipo+id).val('');
                            $("#GENDER_"+tipo+id).val('');
                            $("#BIRTHDATE_"+tipo+id).val('');
                            $("#MOTHER_NAME_"+tipo+id).val('');
                            $("#EMAIL_"+tipo+id).val('');
                            $("input[data-custom-id='ZIPCODE_"+tipo+id+"']").val('');
                            $("select[data-custom-id='STATE_"+tipo+id+"']").val('');
                            $("input[data-custom-id='CITY_"+tipo+id+"']").val('');
                            $("input[data-custom-id='DISTRICT_"+tipo+id+"']").val('');
                            $("input[data-custom-id='STREET_"+tipo+id+"']").val('');
                            $("input[data-custom-id='NUMBER_"+tipo+id+"']").val('');
                            $("input[data-custom-id='COMPLEMENT_"+tipo+id+"']").val('');
                            $("#TELEPHONE_"+tipo+id).val('');
                        } else if (data.cpf) {
                            $("#NAME_"+tipo+id).val(data.name);
                            $("#GENDER_"+tipo+id).val(data.gender);
                            $("#BIRTHDATE_"+tipo+id).val(data.birthdate);
                            $("#MOTHER_NAME_"+tipo+id).val(data.mother_name);
                        } else {
                            $("#NAME_"+tipo+id).val(data.name);
                            $("#EMAIL_"+tipo+id).val(data.email);
                            $("input[data-custom-id='ZIPCODE_"+tipo+id+"']").val(data.zipcode);
                            $("select[data-custom-id='STATE_"+tipo+id+"']").val(data.state);
                            $("input[data-custom-id='CITY_"+tipo+id+"']").val(data.city);
                            $("input[data-custom-id='DISTRICT_"+tipo+id+"']").val(data.district);
                            $("input[data-custom-id='STREET_"+tipo+id+"']").val(data.street);
                            $("input[data-custom-id='NUMBER_"+tipo+id+"']").val(data.number);
                            $("input[data-custom-id='COMPLEMENT_"+tipo+id+"']").val(data.complement);
                            $("#TELEPHONE_"+tipo+id).val(data.telephone);
                        }

                        $("#loading").hide();
                        $("#form_proposal").fadeIn();
                    }
                });
            });

            $("#form_proposal").validate({
                rules: {
                    user_id: { required: true, normalizer: function(value) { return $.trim(value); } },
                    'pay_componentes[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    'pay_metodos[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    'pay_quantidades[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    'pay_validades[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    'pay_valores[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                },
                submitHandler: function(form) {
                    $("#form_proposal").hide();
                    $("#loading").fadeIn();
                    form.submit();
                }
            });

            $.validator.addMethod("range_payment", function(value, element) {
                var day = new Date(value).getUTCDate();
                return ![1,2,3,4,5,29,30,31].includes(day);
            }, 'O dia do vencimento deve estar entre 5 e 28.');

            $.validator.addMethod("divisao", function(value, element) {
                return proportions();
            }, 'A soma das proporções deve ser 100%.');

            $.validator.addMethod("doc", function(value, element) {
                var propo = getPropo(element);

                if ($("#type"+propo).val() == 'Física') {
                    if(cpf(value.replace(/\.|\-/g, ''))) return true;
                } else {
                    if(cnpj(value.replace(/\.|\-|\//g, ''))) return true;
                }

                return false;

            }, 'Documento inválido');

            const getValue = function(target){
                const value = $.trim($(target).attr('data-id'));
                return $("#check_propo"+value).is(":checked");
            }

            const getValue2 = function(target){
                const value = $.trim($(target).attr('data-id'));
                return $("#estado_civil"+value).val() == 'Casado';
            }

            const getPropo = function(target){
                return $.trim($(target).attr('data-id'));
            }

            const dependsCheck = function(element) { return getValue(element); $("#check_propo"+element.attr('data-id')).is(":checked"); };
            const dependsCheck2 = function(element) { return getValue(element) && getValue2(element); };

            jQuery.validator.addClassRules({
                /* DADOS PESSOAIS */
                type: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                doc: { doc: true, required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                name: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                cellphone: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                email: { email: true, required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                gender: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                civil_status: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                birthdate: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                rg: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                emitter: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                rg_uf: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                birthplace: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                country: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                mother_name: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                father_name: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                occupation: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                house: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                gross_income: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                net_income: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                proportion: { divisao: { depends: dependsCheck }, required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                spouse_proportion: { divisao: { depends: dependsCheck2 }, required: { depends: dependsCheck2 }, normalizer: function(value) { return $.trim(value); } },
                /* DADOS RESIDENCIAIS */
                zipcode: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                street: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                number: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                complement: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                district: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                city: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                state: { required: { depends: dependsCheck }, normalizer: function(value) { return $.trim(value); } },
                /* PROPOSTA */
                media: { required: true, normalizer: function(value) { return $.trim(value); } },
                reason: { required: true, normalizer: function(value) { return $.trim(value); } },
                pay_componentes: { required: true, normalizer: function(value) { return $.trim(value); } },
                pay_metodos: { required: true, normalizer: function(value) { return $.trim(value); } },
                pay_quantidades: { required: true, normalizer: function(value) { return $.trim(value); } },
                pay_validades: { range_payment: true, required: true, normalizer: function(value) { return $.trim(value); } },
                pay_valores: { required: true, normalizer: function(value) { return $.trim(value); } }
            });
        });
    </script>
@endsection