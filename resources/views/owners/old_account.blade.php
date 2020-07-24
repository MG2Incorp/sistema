<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Banco</label>
                    <select id="bank_code" class="form-control form-control-sm req banco" name="old_owner_bank_code[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        @foreach(getBanks() as $key => $bank)
                            <option value="{{ $key }}" {{ $account->bank_code == $key ? 'selected' : '' }}>{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Agência</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control req first" name="old_owner_agency[{{ $account->id }}]" value="{{ $account->agency }}" required>
                        <span class="input-group-append"><span class="input-group-text">-</span></span>
                        <input type="text" class="form-control second" name="old_owner_agency_dv[{{ $account->id }}]" value="{{ $account->agency_dv }}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Número</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control req first" name="old_owner_account_number[{{ $account->id }}]" value="{{ $account->number }}" required>
                        <span class="input-group-append"><span class="input-group-text">-</span></span>
                        <input type="text" class="form-control second" name="old_owner_account_number_dv[{{ $account->id }}]" value="{{ $account->number_dv }}">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Tipo</label>
                    <select class="form-control form-control-sm req" name="old_owner_account_type[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        <option value="CORRENTE" {{ $account->type == 'CORRENTE' ? 'selected' : '' }}>Corrente</option>
                        <option value="POUPANÇA" {{ $account->type == 'POUPANÇA' ? 'selected' : '' }}>Poupança</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Cód. Beneficiário</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $account->beneficiario }}" name="old_owner_beneficiario[{{ $account->id }}]">
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Cód. Empresa</label>
                    <input type="text" class="form-control form-control-sm" value="{{ $account->company_code }}" name="old_owner_company_code[{{ $account->id }}]">
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control form-control-sm req" name="old_owner_account_status[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        <option value="ACTIVE" {{ $account->status == 'ACTIVE' ? 'selected' : '' }}>Ativo</option>
                        <option value="INATIVE" {{ $account->status == 'INATIVE' ? 'selected' : '' }}>Inativo</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Número Convênio</label>
                    <input type="text" name="old_agreement_numero[{{ $account->id }}]" class="form-control form-control-sm" value="{{ @$account->agreement->numero }}" required>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="old_agreement_descricao[{{ $account->id }}]" class="form-control form-control-sm" value="{{ @$account->agreement->descricao }}" required>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Carteira</label>
                    <input type="text" name="old_agreement_carteira[{{ $account->id }}]" class="form-control form-control-sm" value="{{ @$account->agreement->carteira }}" required>
                </div>
            </div>
            <!-- <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Espécie</label>
                    <select class="form-control form-control-sm" name="old_agreement_especie[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        <option value="R$" {{ $account->agreement && $account->agreement->especie == 'R$' ? 'selected' : '' }}>R$</option>
                        <option value="US$" {{ $account->agreement && $account->agreement->especie == 'US$' ? 'selected' : '' }}>US$</option>
                        <option value="IGPM" {{ $account->agreement && $account->agreement->especie == 'IGPM' ? 'selected' : '' }}>IGPM</option>
                    </select>
                </div>
            </div> -->
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>CNAB</label>
                    <select class="form-control form-control-sm cnab" name="old_agreement_padrao[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        <option value="240" {{ $account->agreement && $account->agreement->cnab == '240' ? 'selected' : '' }}>240</option>
                        <option value="400" {{ $account->agreement && $account->agreement->cnab == '400' ? 'selected' : '' }}>400</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Reiniciar diariamente</label>
                    <select class="form-control form-control-sm reiniciar" name="old_agreement_reiniciar[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        <option value="1" {{ $account->agreement && $account->agreement->reiniciar == '1' ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ $account->agreement && $account->agreement->reiniciar == '0' ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2 numero_remessa {{ $account->agreement && !$account->agreement->reiniciar ? '' : 'd-none' }}">
                <div class="form-group">
                    <label>Número da remessa</label>
                    <input type="text" name="old_agreement_numero_remessa[{{ $account->id }}]" class="form-control form-control-sm remessa" value="{{ @$account->agreement->numero_remessa }}">
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Transmissão automática</label>
                    <select class="form-control form-control-sm" name="old_agreement_utiliza_van[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        <option value="1" {{ $account->agreement && $account->agreement->utiliza_van == '1' ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ $account->agreement && $account->agreement->utiliza_van == '0' ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2 div_densidade {{ $account->agreement && $account->bank_code == '033' && $account->agreement->cnab == '240' ? '' : 'd-none' }}">
                <div class="form-group">
                    <label>Densidade de remessa</label>
                    <select class="form-control form-control-sm densidade_remessa" name="old_agreement_densidade_remessa[{{ $account->id }}]">
                        <option value="">Selecione...</option>
                        <option value="1600" {{ $account->agreement && $account->agreement->densidade_remessa == '1600' ? 'selected' : '' }}>1600</option>
                        <option value="6250" {{ $account->agreement && $account->agreement->densidade_remessa == '6250' ? 'selected' : '' }}>6250</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label>Nosso Número controlado pelo banco?</label>
                    <select class="form-control form-control-sm" name="old_agreement_nosso_numero_banco[{{ $account->id }}]" required>
                        <option value="">Selecione...</option>
                        <option value="1" {{ $account->agreement && $account->agreement->nosso_numero_banco == '1' ? 'selected' : '' }}>Sim</option>
                        <option value="0" {{ $account->agreement && $account->agreement->nosso_numero_banco == '0' ? 'selected' : '' }}>Não</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-3">
                <div class="form-group">
                    <label>Início Nosso Número</label>
                    <input type="text" name="old_owner_inicio_nosso_numero[{{ $account->id }}]" class="form-control form-control-sm" value="{{ $account->inicio_nosso_numero }}">
                </div>
            </div>
        </div>
    </div>
</div>