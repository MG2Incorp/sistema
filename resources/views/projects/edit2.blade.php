@extends('layouts.app')
@section('css')
    <link href="{{ asset('date/datedropper.min.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" type="text/css" href="{{ asset('css/choices.css') }}">
@endsection
@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12">
                <div class="card box border-warning">
                    <div class="card-header">Editar empreendimento</div>
                    <div class="card-body">
                        <ul class="nav nav-tabs" id="myTab" role="tablist">
                            <li class="nav-item"><a class="nav-link active" data-toggle="tab" href="#informations" role="tab">Informações</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#mapping" role="tab">Mapeamento</a></li>
                            <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#owners" role="tab">Proprietários</a></li>
                            @if($project->all_accounts->count())
                                <li class="nav-item"><a class="nav-link" data-toggle="tab" href="#billing" role="tab">Financeiro</a></li>
                            @endif
                        </ul>
                        <div class="tab-content">
                            <div class="tab-pane active" id="informations" role="tabpanel">
                                <div class="card border-top-0">
                                    <div class="card-body">
                                        <form action="{{ action('ProjectController@update', $project->id) }}" method="POST" enctype="multipart/form-data" id="form_project">
                                            {{ csrf_field() }}
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label>Razão Social</label>
                                                        <input type="text" name="social_name" class="form-control" value="{{ $project->social_name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>CNPJ</label>
                                                        <input type="text" name="cnpj" class="form-control cnpj" value="{{ $project->cnpj }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-6">
                                                    <div class="form-group">
                                                        <label>Nome</label>
                                                        <input type="text" name="name" class="form-control" value="{{ $project->name }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Previsão de entrega</label>
                                                        <input type="date" name="finish" class="form-control data" value="{{ $project->finish_at }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Status da construção</label>
                                                        <select class="form-control" name="status" required>
                                                            <option value="Em obra" {{ $project->status == 'Em obra' ? 'selected' : '' }}>Em obra</option>
                                                            <option value="Concluído" {{ $project->status == 'Concluído' ? 'selected' : '' }}>Concluído</option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Tipo</label>
                                                        <select class="form-control" name="type" required>
                                                            <option value="Andar" {{ $project->type == 'Andar' ? 'selected' : '' }}>Construção</option>
                                                            <option value="Quadra" {{ $project->type == 'Quadra' ? 'selected' : '' }}>Loteamento</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Local</label>
                                                        <input type="text" name="local" class="form-control" id="local" autocomplete="off" value="{{ $project->local }}" required>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Ativar simulador?</label>
                                                        <select name="simulator" class="form-control" required>
                                                            <option value="1" {{ $project->simulator == 1 ? 'selected' : '' }}>Sim</option>
                                                            <option value="0" {{ $project->simulator == 0 ? 'selected' : '' }}>Não</option>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="col-12 col-sm-4" id="div_simulator" style="display: {{ $project->simulator ? 'block' : 'none' }}">
                                                    <div class="row">
                                                        <div class="col-12 col-sm-5">
                                                            <div class="form-group">
                                                                <label>Taxa</label>
                                                                <input type="text" name="fee" class="form-control money" value="{{ formatMoney($project->fee) }}">
                                                            </div>
                                                        </div>
                                                        <div class="col-12 col-sm-7">
                                                            <div class="form-group">
                                                                <label>Porcentagem Mínima</label>
                                                                <div class="input-group">
                                                                    <input type="text" name="minimum_percentage" class="form-control money" value="{{ formatMoney($project->minimum_percentage) }}">
                                                                    <div class="input-group-prepend"><span class="input-group-text">%</span></div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Foto</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="inputGroupFile04" name="file">
                                                                <label class="custom-file-label" for="inputGroupFile04">Selecionar</label>
                                                            </div>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_photo"><i class="fas fa-eye"></i></button>
                                                            </div>
                                                        </div>
                                                        <small class="form-text text-muted">Recomendado: Larg: 150px / Alt: 190px</small>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Tempo para expiração</label>
                                                        <div class="input-group">
                                                            <input type="text" name="time" class="form-control" value="{{ $project->expiration_time }}" required>
                                                            <div class="input-group-prepend"><span class="input-group-text">h</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-2">
                                                    <div class="form-group">
                                                        <label>Comissão</label>
                                                        <div class="input-group">
                                                            <input type="text" name="comission" class="form-control money" value="{{ formatMoney($project->comission) }}" required>
                                                            <div class="input-group-prepend"><span class="input-group-text">%</span></div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Imagem para Proposta</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="inputGroupFile06" name="file2">
                                                                <label class="custom-file-label" for="inputGroupFile06">Selecionar</label>
                                                            </div>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_photo2"><i class="fas fa-eye"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <small class="form-text text-muted">Recomendado: Larg: 280px / Alt: 150px</small>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="form-group">
                                                        <label>Observações de registro</label>
                                                        <input type="text" name="notes" class="form-control" value="{{ $project->notes }}" required>
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <label>Blocos</label>
                                            <div class="row" id="buildings">
                                                <div class="col-12 col-sm-1">
                                                    <div class="form-group">
                                                        <button type="button" class="btn btn-info btn-block" id="add"><i class="fas fa-plus"></i></button>
                                                    </div>
                                                </div>
                                                @foreach($project->buildings as $building)
                                                    <div class="col-12 col-sm-3">
                                                        <div class="form-group">
                                                            <input type="text" name="old_buildings[{{ $building->id }}]" class="form-control" value="{{ $building->name }}">
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                            <hr>
                                            <label>Imobiliárias</label>
                                            <div class="row">
                                                <div class="col-12">
                                                    <select class="form-control js-choice2" name="companies[]" required multiple>
                                                        @foreach($companies as $company)
                                                            <option value="{{ $company->id }}" {{ $project->companies->contains('id', $company->id) ? 'selected' : '' }}>{{ $company->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <hr>
                                            <label>Índices</label>
                                            <div class="row">
                                                <div class="col-12">
                                                    @php $old_indexes = explode(',', $project->indexes); @endphp
                                                    <select class="form-control js-choice2" name="indexes[]" required multiple>
                                                        @foreach($indexes as $index)
                                                            <option value="{{ $index->id }}" {{ in_array($index->id, $old_indexes) ? 'selected' : '' }}>{{ $index->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <hr>
                                            <div class="row">
                                                <div class="col-12 col-sm-4">
                                                    <div class="form-group">
                                                        <label>Leading - Background</label>
                                                        <div class="input-group">
                                                            <div class="custom-file">
                                                                <input type="file" class="custom-file-input" id="inputGroupFile05" name="bg">
                                                                <label class="custom-file-label" for="inputGroupFile05">Selecionar</label>
                                                            </div>
                                                            <div class="input-group-append">
                                                                <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_bg"><i class="fas fa-eye"></i></button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-3">
                                                    <div class="form-group">
                                                        <label>Ativar chat?</label>
                                                        <select name="chat" class="form-control" id="chat">
                                                            <option value="1" {{ $project->chat == 1 ? 'selected' : '' }}>Sim</option>
                                                            <option value="0" {{ $project->chat == 0 ? 'selected' : '' }}>Não</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-12 col-sm-4" style="display: {{ $project->chat ? 'block' : 'none' }}" id="div_codigo_jivochat">
                                                    <div class="form-group">
                                                        <label>Código Jivochat (Widget ID apenas)</label>
                                                        <input type="text" name="chat_code" class="form-control" value="{{ $project->chat_code }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <hr>
                                            <button type="submit" class="btn btn-success">Salvar empreendimento</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="mapping" role="tabpanel">
                                <div class="card border-top-0">
                                    <div class="card-body">
                                        <form action="{{ action('ProjectController@map') }}" method="POST" enctype="multipart/form-data">
                                            {{ csrf_field() }}
                                            <input type="hidden" name="project_id" value="{{ $project->id }}">
                                            <div class="form-group">
                                                <label>Imagem do Mapa (Tamanho recomendado: 1920px x 1080px)</label>
                                                <div class="input-group">
                                                    <div class="custom-file">
                                                        <input type="file" class="custom-file-input" id="inputGroupFile07" name="map">
                                                        <label class="custom-file-label" for="inputGroupFile07">Selecionar</label>
                                                    </div>
                                                    <div class="input-group-append">
                                                        <button class="btn btn-outline-secondary" type="button" data-toggle="modal" data-target="#show_photo3"><i class="fas fa-eye"></i></button>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="alert alert-primary" role="alert">
                                                Para a obtenção das coordenadas, <a href="https://www.image-map.net/" class="alert-link" target="_BLANK">clique aqui</a>.
                                            </div>
                                            <div class="form-group">
                                                <label>Campos para exibição</label>
                                                <select class="form-control js-choice2" name="fields[]" multiple>
                                                    <?php
                                                        $fields = [ 
                                                            'bloco' => 'Bloco', 
                                                            'quadra' => 'Quadra', 
                                                            'number' => 'Número', 
                                                            'value' => 'Valor', 
                                                            'notes' => 'Observações', 
                                                            'size' => 'Área', 
                                                            'dimensions' => 'Dimensão', 
                                                            'situation' => 'Situação',
                                                            'status' => 'Status',
                                                            'numero_matricula' => 'Número da Matrícula', 
                                                            'cadastro_imobiliario' => 'Cadastro Imobiliário',
                                                        ];
                                                    ?>
                                                    @if($project->fields)
                                                        @foreach(json_decode($project->fields) as $key => $field)
                                                            <option value="{{ $field }}" selected>{{ $fields[$field] }}</option>
                                                            <?php unset($fields[$field]); ?>
                                                        @endforeach
                                                    @endif
                                                    @foreach($fields as $key => $field)
                                                        <option value="{{ $key }}">{{ $field }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label>Mapeamento</label>
                                                <table class="table table-sm table-bordered table-hover m-0 text-center">
                                                    <thead>
                                                        <tr>
                                                            <th width="10%">Bloco</th>
                                                            <th width="10%">Andar</th>
                                                            <th width="10%">Número</th>
                                                            <th width="15%">Forma</th>
                                                            <th>Coordenadas</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($project->properties as $property)
                                                            <tr>
                                                                <td>{{ $property->block->building->name }} <input type="hidden" name="properties[]" value="{{ $property->id }}"></td>
                                                                <td>{{ $property->block->label }}</td>
                                                                <td>{{ $property->number }}</td>
                                                                <td>
                                                                    <select name="shapes[{{ $property->id }}]" class="form-control">
                                                                        <option value="">Selecione...</option>
                                                                        <option value="rect" {{ $property->map && $property->map->shape == 'rect' ? 'selected' : '' }}>Rect</option>
                                                                        <option value="poly" {{ $property->map && $property->map->shape == 'poly' ? 'selected' : '' }}>Poly</option>
                                                                        <option value="circle" {{ $property->map && $property->map->shape == 'circle' ? 'selected' : '' }}>Circle</option>
                                                                    </select>
                                                                </td>
                                                                <td><input type="text" name="coords[{{ $property->id }}]" class="form-control" value="{{ $property->map ? $property->map->coordinates : '' }}"></td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                            <hr>
                                            <button type="submit" class="btn btn-success">Salvar mapeamento</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="tab-pane" id="owners" role="tabpanel">
                                <div class="card border-top-0">
                                    <div class="card-body">
                                        <div class="alert alert-danger" role="alert">
                                            <strong>Atenção!</strong> Contas que já estiverem com lotes vinculados não serão removidas.
                                        </div>
                                        @if($owners->count())
                                            <form action="{{ action('ProjectController@owner') }}" method="POST" enctype="multipart/form-data" id="form_owners">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                <div class="form-group">
                                                    <label>Selecione os proprietários do empreendimento</label>
                                                    <select name="select_owners[]" class="form-control js-choice" multiple id="select_owner" required>
                                                        @foreach($owners as $owner)
                                                            <option value="{{ $owner->id }}" {{ $project->owners->count() && in_array($owner->id, $project->owners->pluck('id')->toArray()) ? 'selected' : '' }}>{{ $owner->alias }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                <hr>
                                                <div id="accounts">
                                                    @if($project->owners->count())
                                                        @foreach($project->owners->unique() as $own)
                                                            <div class="form-group">
                                                                <label>Selecione as contas do proprietário <b>{{ $own->alias }}</b></label>
                                                                <select name="select_owner[{{ $own->id }}][]" class="form-control js-choice select_account" multiple>
                                                                    @foreach($own->accounts->where('status', 'ACTIVE') as $key => $account)
                                                                        <option value="{{ $account->id }}" {{ in_array($account->id, $project->accounts->pluck('account_id')->toArray()) ? 'selected' : '' }}>{{ getBankCode($account->bank_code) }} | Ag. {{ $account->agency.'-'.$account->agency_dv }} | Num. {{ $account->number.'-'.$account->number_dv }}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>
                                                        @endforeach
                                                    @endif
                                                </div>
                                                <hr>
                                                <button type="submit" class="btn btn-success">Salvar proprietários</button>
                                            </form>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            @if($project->all_accounts->count())
                                <div class="tab-pane" id="billing" role="tabpanel">
                                    <form action="{{ action('ProjectController@billing_method') }}" class="row mt-4" method="POST">
                                        {{ csrf_field() }}
                                        <input type="hidden" name="project_id" value="{{ $project->id }}">
                                        <div class="col-12 col-sm-4">
                                            <div class="form-group">
                                                <label>Geração e envio de boletos de cobranças</label>
                                                <select name="send_billets" class="form-control" required>
                                                    <option value="">Selecione...</option>
                                                    <option value="MES" {{ $project->send_billets == 'MES' ? 'selected' : '' }}>Mensalmente</option>
                                                    <option value="CICLO" {{ $project->send_billets == 'CICLO' ? 'selected' : '' }}>No início do ciclo de reajuste</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-12 col-sm-4 d-flex flex-column justify-content-end">
                                            <div class="form-group">
                                                <button type="submit" class="btn btn-success">Salvar</button>
                                            </div>
                                        </div>
                                    </form>
                                    <hr>
                                    <div class="accordion mt-4" id="acc2">
                                        @foreach($project->all_accounts as $all_accounts)
                                            <?php $aux_all_accounts = $project->accounts->where('account_id', $all_accounts->id)->first(); ?>
                                            <form action="{{ action('ProjectController@billing') }}" method="POST">
                                                {{ csrf_field() }}
                                                <input type="hidden" name="account_id" value="{{ $all_accounts->id }}">
                                                <input type="hidden" name="project_id" value="{{ $project->id }}">
                                                <div class="card">
                                                    <div class="card-header" id="heading{{ $all_accounts->id }}" data-toggle="collapse" data-target="#collapse{{ $all_accounts->id }}">{{ getBankCode($all_accounts->bank_code) }} | Ag. {{ $all_accounts->agency.'-'.$all_accounts->agency_dv }} | Num. {{ $all_accounts->number.'-'.$all_accounts->number_dv }}</div>
                                                    <div id="collapse{{ $all_accounts->id }}" class="collapse" data-parent="#acc2">
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Espécie do Documento</label>
                                                                        <select name="TituloDocEspecie" class="form-control" required>
                                                                            <option value="">Selecione...</option>
                                                                            <option value="<?php echo '01'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '01' ? 'selected' : '' }}>01 - Duplicata Mercantil</option>
                                                                            <option value="<?php echo '02'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '02' ? 'selected' : '' }}>02 - Nota promissória</option>
                                                                            <option value="<?php echo '03'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '03' ? 'selected' : '' }}>03 - Nota de seguro</option>
                                                                            <option value="<?php echo '04'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '04' ? 'selected' : '' }}>04 - Duplicata de Serviço</option>
                                                                            <option value="<?php echo '05'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '05' ? 'selected' : '' }}>05 - Recibo</option>
                                                                            <option value="<?php echo '06'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '06' ? 'selected' : '' }}>06 - Letra de Câmbio</option>
                                                                            <option value="<?php echo '07'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '07' ? 'selected' : '' }}>07 - Nota de Débito</option>
                                                                            <option value="<?php echo '08'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '08' ? 'selected' : '' }}>08 - Boleto de Proposta</option>
                                                                            <option value="<?php echo '09'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '09' ? 'selected' : '' }}>09 - Letra de Câmbio</option>
                                                                            <option value="<?php echo '10'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '10' ? 'selected' : '' }}>10 - Warrant</option>
                                                                            <option value="<?php echo '11'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '11' ? 'selected' : '' }}>11 - Cheque</option>
                                                                            <option value="<?php echo '12'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '12' ? 'selected' : '' }}>12 - Cobrança Seriada</option>
                                                                            <option value="<?php echo '13'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '13' ? 'selected' : '' }}>13 - Mensalidade escolar</option>
                                                                            <option value="<?php echo '14'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '14' ? 'selected' : '' }}>14 - Apólice de Seguro</option>
                                                                            <option value="<?php echo '15'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '15' ? 'selected' : '' }}>15 - Documento de Dívida</option>
                                                                            <option value="<?php echo '16'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '16' ? 'selected' : '' }}>16 - Encargos Condominiais</option>
                                                                            <option value="<?php echo '17'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '17' ? 'selected' : '' }}>17 - Conta de prestação de serviço</option>
                                                                            <option value="<?php echo '18'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '18' ? 'selected' : '' }}>18 - Contrato</option>
                                                                            <option value="<?php echo '19'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '19' ? 'selected' : '' }}>19 - Cosseguro</option>
                                                                            <option value="<?php echo '20'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '20' ? 'selected' : '' }}>20 - Duplicata Rural</option>
                                                                            <option value="<?php echo '21'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '21' ? 'selected' : '' }}>21 - Nota Promissória Rural</option>
                                                                            <option value="<?php echo '22'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '22' ? 'selected' : '' }}>22 - Dívida Ativa da União</option>
                                                                            <option value="<?php echo '23'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '23' ? 'selected' : '' }}>23 - Dívida Ativa de Estado</option>
                                                                            <option value="<?php echo '24'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '24' ? 'selected' : '' }}>24 - Dívida Ativa de Município</option>
                                                                            <option value="<?php echo '25'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '25' ? 'selected' : '' }}>25 - Duplicata Mercantil por Indicação</option>
                                                                            <option value="<?php echo '26'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '26' ? 'selected' : '' }}>26 - Duplicata de Serviço por Indicação</option>
                                                                            <option value="<?php echo '27'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '27' ? 'selected' : '' }}>27 - Nota de Crédito Comercial</option>
                                                                            <option value="<?php echo '28'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '28' ? 'selected' : '' }}>28 - Nota de Crédito para Exportação</option>
                                                                            <option value="<?php echo '29'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '29' ? 'selected' : '' }}>29 - Nota de Crédito Industrial</option>
                                                                            <option value="<?php echo '30'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '30' ? 'selected' : '' }}>30 - Nota de Crédito Rural</option>
                                                                            <option value="<?php echo '32'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '32' ? 'selected' : '' }}>32 - Triplicata Mercantil</option>
                                                                            <option value="<?php echo '33'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '33' ? 'selected' : '' }}>33 - Triplicata de Serviço</option>
                                                                            <option value="<?php echo '34'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '34' ? 'selected' : '' }}>34 - Fatura</option>
                                                                            <option value="<?php echo '35'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '35' ? 'selected' : '' }}>35 - Parcela de Consórcio</option>
                                                                            <option value="<?php echo '36'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '36' ? 'selected' : '' }}>36 - Nota Fiscal</option>
                                                                            <option value="<?php echo '37'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '37' ? 'selected' : '' }}>37 - Cédula de Produto Rural</option>
                                                                            <option value="<?php echo '38'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '38' ? 'selected' : '' }}>38 - Cartão de crédito</option>
                                                                            <option value="<?php echo '99'; ?>" {{ $aux_all_accounts->TituloDocEspecie == '99' ? 'selected' : '' }}>99 - Outros</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Data para Desconto</label>
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control" name="TituloDataDesconto" value="{{ $aux_all_accounts->TituloDataDesconto }}">
                                                                            <span class="input-group-append"><span class="input-group-text">dias</span></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Código do Desconto</label>
                                                                        <select name="TituloCodDesconto" class="form-control">
                                                                            <option value="">Selecione...</option>
                                                                            <option value="0" {{ $aux_all_accounts->TituloCodDesconto == '0' ? 'selected' : '' }}>Sem instrução de desconto</option>
                                                                            <option value="1" {{ $aux_all_accounts->TituloCodDesconto == '1' ? 'selected' : '' }}>Valor Fixo Até a Data Informada</option>
                                                                            <option value="2" {{ $aux_all_accounts->TituloCodDesconto == '2' ? 'selected' : '' }}>Percentual Até a Data Informada</option>
                                                                            <option value="3" {{ $aux_all_accounts->TituloCodDesconto == '3' ? 'selected' : '' }}>Valor por Antecipação Dia Corrido</option>
                                                                            <option value="4" {{ $aux_all_accounts->TituloCodDesconto == '4' ? 'selected' : '' }}>Valor por Antecipação Dia Útil</option>
                                                                            <option value="5" {{ $aux_all_accounts->TituloCodDesconto == '5' ? 'selected' : '' }}>Percentual Sobre o Valor Nominal Dia Corrido</option>
                                                                            <option value="6" {{ $aux_all_accounts->TituloCodDesconto == '6' ? 'selected' : '' }}>Percentual Sobre o Valor Nominal Dia Útil</option>
                                                                            <option value="7" {{ $aux_all_accounts->TituloCodDesconto == '7' ? 'selected' : '' }}>Cancelamento de Desconto</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Taxa/Valor do Desconto</label>
                                                                        <input type="text" class="form-control money" name="TituloValorDescontoTaxa" value="{{ formatMoney($aux_all_accounts->TituloValorDescontoTaxa) }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Data para Juros</label>
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control" name="TituloDataJuros" value="{{ $aux_all_accounts->TituloDataJuros }}">
                                                                            <span class="input-group-append"><span class="input-group-text">dias</span></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Código do Juros</label>
                                                                        <select name="TituloCodigoJuros" class="form-control">
                                                                            <option value="">Selecione...</option>
                                                                            <option value="1" {{ $aux_all_accounts->TituloCodigoJuros == '1' ? 'selected' : '' }}>Valor por dia</option>
                                                                            <option value="2" {{ $aux_all_accounts->TituloCodigoJuros == '2' ? 'selected' : '' }}>Taxa mensal</option>
                                                                            <option value="3" {{ $aux_all_accounts->TituloCodigoJuros == '3' ? 'selected' : '' }}>Isento</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Taxa/Valor do Juros</label>
                                                                        <input type="text" class="form-control money" name="TituloValorJuros" value="{{ formatMoney($aux_all_accounts->TituloValorJuros) }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Data para Multa</label>
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control" name="TituloDataMulta" value="{{ $aux_all_accounts->TituloDataMulta }}">
                                                                            <span class="input-group-append"><span class="input-group-text">dias</span></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Código da Multa</label>
                                                                        <select name="TituloCodigoMulta" class="form-control">
                                                                            <option value="">Selecione...</option>
                                                                            <option value="0" {{ $aux_all_accounts->TituloCodigoMulta == '0' ? 'selected' : '' }}>Não registra a multa</option>
                                                                            <option value="1" {{ $aux_all_accounts->TituloCodigoMulta == '1' ? 'selected' : '' }}>Valor em Reais (Fixo)</option>
                                                                            <option value="2" {{ $aux_all_accounts->TituloCodigoMulta == '2' ? 'selected' : '' }}>Valor em percentual com duas casas decimais</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Taxa/Valor da multa</label>
                                                                        <input type="text" class="form-control money" name="TituloValorMultaTaxa" value="{{ formatMoney($aux_all_accounts->TituloValorMultaTaxa) }}">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Código para protesto</label>
                                                                        <select name="TituloCodProtesto" class="form-control">
                                                                            <option value="">Selecione...</option>
                                                                            <option value="1" {{ $aux_all_accounts->TituloCodProtesto == '1' ? 'selected' : '' }}>Protestar Dias Corridos</option>
                                                                            <option value="2" {{ $aux_all_accounts->TituloCodProtesto == '2' ? 'selected' : '' }}>Protestar Dias Úteis</option>
                                                                            <option value="3" {{ $aux_all_accounts->TituloCodProtesto == '3' ? 'selected' : '' }}>Não Protestar</option>
                                                                            <option value="4" {{ $aux_all_accounts->TituloCodProtesto == '4' ? 'selected' : '' }}>Protestar Fim Falimentar - Dias Úteis</option>
                                                                            <option value="5" {{ $aux_all_accounts->TituloCodProtesto == '5' ? 'selected' : '' }}>Protestar Fim Falimentar - Dias Corridos</option>
                                                                            <option value="8" {{ $aux_all_accounts->TituloCodProtesto == '8' ? 'selected' : '' }}>Negativação sem Protesto</option>
                                                                            <option value="9" {{ $aux_all_accounts->TituloCodProtesto == '9' ? 'selected' : '' }}>Cancelamento Protesto Automático</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Prazo para Protesto</label>
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control" name="TituloPrazoProtesto" value="{{ $aux_all_accounts->TituloPrazoProtesto }}">
                                                                            <span class="input-group-append"><span class="input-group-text">dias</span></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Código para Baixa Automática</label>
                                                                        <select name="TituloCodBaixaDevolucao" class="form-control">
                                                                            <option value="">Selecione...</option>
                                                                            <option value="1" {{ $aux_all_accounts->TituloCodBaixaDevolucao == '1' ? 'selected' : '' }}>Baixar/Devolver</option>
                                                                            <option value="2" {{ $aux_all_accounts->TituloCodBaixaDevolucao == '2' ? 'selected' : '' }}>Não baixar / Não devolver</option>
                                                                            <option value="3" {{ $aux_all_accounts->TituloCodBaixaDevolucao == '3' ? 'selected' : '' }}>Cancelar prazo para baixa / Devolução</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Prazo para Baixa</label>
                                                                        <div class="input-group">
                                                                            <input type="text" class="form-control" name="TituloPrazoBaixa" value="{{ $aux_all_accounts->TituloPrazoBaixa }}">
                                                                            <span class="input-group-append"><span class="input-group-text">dias</span></span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-6">
                                                                    <div class="form-group">
                                                                        <label>Local de Pagamento</label>
                                                                        <input type="text" class="form-control" name="TituloLocalPagamento" value="{{ $aux_all_accounts->TituloLocalPagamento }}" maxlength="200">
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Aceite</label>
                                                                        <select name="TituloAceite" class="form-control" required>
                                                                            <option value="">Selecione...</option>
                                                                            <option value="S" {{ $aux_all_accounts->TituloAceite == 'S' ? 'selected' : '' }}>S</option>
                                                                            <option value="N" {{ $aux_all_accounts->TituloAceite == 'N' ? 'selected' : '' }}>N</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-12 col-sm-3">
                                                                    <div class="form-group">
                                                                        <label>Código Emissão do Bloqueto</label>
                                                                        <select name="TituloCodEmissaoBloqueto" class="form-control" required>
                                                                            <option value="">Selecione...</option>
                                                                            <option value="0" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '0' ? 'selected' : '' }}>0 - Não aceita</option>
                                                                            <option value="1" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '1' ? 'selected' : '' }}>1 - Banco Emite</option>
                                                                            <option value="2" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '2' ? 'selected' : '' }}>2 - Cliente Emite</option>
                                                                            <option value="3" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '3' ? 'selected' : '' }}>3 - Banco Pré-emite e Cliente Complementa</option>
                                                                            <option value="4" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '4' ? 'selected' : '' }}>4 - Banco Reemite</option>
                                                                            <option value="5" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '5' ? 'selected' : '' }}>5 - Banco Não Reemite</option>
                                                                            <option value="7" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '7' ? 'selected' : '' }}>7 - Banco Emitente - Aberta</option>
                                                                            <option value="8" {{ $aux_all_accounts->TituloCodEmissaoBloqueto == '8' ? 'selected' : '' }}>8 - Banco Emitente - Auto-envelopável</option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-footer d-flex justify-content-end">
                                                            <button type="submit" class="btn btn-success">Salvar informações financeiras dessa conta</button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_photo" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="image_thumb" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->photo) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_photo2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="image_thumb2" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->photo2) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_photo3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="image_thumb3" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->map) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="show_bg" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body" align="center">
                    <img id="bg_thumb" src="{{ asset(env('PROJECTS_IMAGES_DIR').$project->background_image) }}" style="max-width: 100%">
                </div>
            </div>
        </div>
    </div>
@endsection
@section('js')
    <script type="text/javascript" src="{{ asset('date/datedropper.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/choices.js') }}"></script>
    <script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCA_j74BkWoTK9_q-vGr2qfjU8A8UIp_fA&amp;libraries=places"></script>
    <script>
        function init() {
			var input = document.getElementById('local');
            var autocomplete = new google.maps.places.Autocomplete(input);
		}

        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#image_thumb').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURL2(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#bg_thumb').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURL3(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#image_thumb2').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function readURL4(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                reader.onload = function(e) { $('#image_thumb3').attr('src', e.target.result); }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $(document).ready(function(){
            init();

            const choices = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholder: true, placeholderValue: 'Selecione...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: '' });
            const choices2 = new Choices('.js-choice2', { shouldSort: false, removeItemButton: true, searchEnabled: true, paste: false, placeholderValue: 'Selecione...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: 'Pressione para selecionar' });

            // $(".data").dateDropper();

            $("#inputGroupFile04").change(function() { readURL(this); });
            $("#inputGroupFile05").change(function() { readURL2(this); });
            $("#inputGroupFile06").change(function() { readURL3(this); });
            $("#inputGroupFile07").change(function() { readURL4(this); });

            $(document).on('click', '#add', function(){
                $("#buildings").append('<div class="col-12 col-sm-3"><div class="form-group"><div class="input-group"><input type="text" name="buildings[]" class="form-control"><div class="input-group-append"><button type="button" class="btn btn-danger remove"><i class="far fa-trash-alt"></i></button></div></div></div></div>');
            });

            $(document).on('click', '.remove', function() { $(this).parent().parent().parent().parent().remove(); });

            $(document).on('change', '#select_owner', function() {
                var array = new Array();
                $(".select_account").each(function() { array.push($(this).val()); })
                $('#accounts').html('');

                $.ajax({
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }, url: "{{ URL::route('owners.search') }}", type: "POST", data: { id: $(this).val(), array: array }, cache: false, processData:true,
                    success: function(data) {
                        $("#accounts").html(data);
                        const choices = new Choices('.js-choice', { removeItemButton: true, searchEnabled: true, paste: false, placeholder: true, placeholderValue: 'Selecione...', searchPlaceholderValue: 'Digite para buscar...', itemSelectText: '' });
                    }
                });
            });

            $(document).on('change', '#simulator', function(){
                if($(this).val() == 1) {
                    $("#div_simulator").show();
                } else {
                    $("#div_simulator").hide();
                }
            });

            $(document).on('change', '#chat', function(){
                if($(this).val() == 1) {
                    $("#div_codigo_jivochat").show();
                } else {
                    $("#div_codigo_jivochat").hide();
                }
            });

            const activeChat = function(element) { return $("#chat").val() == 1; };

            $("#form_project").validate({
                rules: {
                    'select_owners[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    'old_buildings[]': { required: true, normalizer: function(value) { return $.trim(value); } },
                    chat_code: { required: { depends: activeChat }, normalizer: function(value) { return $.trim(value); } }
                },
                submitHandler: function(form) { form.submit(); }
            });

            $("#form_owners").validate();
        });
    </script>
@endsection