<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Banco</label>
                    <select id="bank_code" class="form-control form-control-sm req banco" name="owner_bank_code[]" required>
                        <option value="">Selecione...</option>
                        @foreach(getBanks() as $key => $bank)
                            <option value="{{ $key }}">{{ $bank }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Agência</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control req first" name="owner_agency[]" required>
                        <span class="input-group-append"><span class="input-group-text">-</span></span>
                        <input type="text" class="form-control second" name="owner_agency_dv[]">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Número</label>
                    <div class="input-group input-group-sm">
                        <input type="text" class="form-control req first" name="owner_account_number[]" required>
                        <span class="input-group-append"><span class="input-group-text">-</span></span>
                        <input type="text" class="form-control second" name="owner_account_number_dv[]">
                    </div>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Tipo</label>
                    <select class="form-control form-control-sm req" name="owner_account_type[]" required>
                        <option value="">Selecione...</option>
                        <option value="CORRENTE">Corrente</option>
                        <option value="POUPANÇA">Poupança</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Cód. Beneficiário</label>
                    <input type="text" class="form-control form-control-sm" name="owner_beneficiario[]">
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Cód. Empresa</label>
                    <input type="text" class="form-control form-control-sm" name="owner_company_code[]">
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control form-control-sm req" name="owner_account_status[]" required>
                        <option value="">Selecione...</option>
                        <option value="ACTIVE">Ativo</option>
                        <option value="INATIVE">Inativo</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Número Convênio</label>
                    <input type="text" name="agreement_numero[]" class="form-control form-control-sm" value="" required>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Descrição</label>
                    <input type="text" name="agreement_descricao[]" class="form-control form-control-sm" value="" required>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Carteira</label>
                    <input type="text" name="agreement_carteira[]" class="form-control form-control-sm" value="" required>
                </div>
            </div>
            <!-- <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Espécie</label>
                    <select class="form-control form-control-sm" name="agreement_especie[]" required>
                        <option value="">Selecione...</option>
                        <option value="R$">R$</option>
                        <option value="US$">US$</option>
                        <option value="IGPM">IGPM</option>
                    </select>
                </div>
            </div> -->
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>CNAB</label>
                    <select class="form-control form-control-sm cnab" name="agreement_padrao[]" required>
                        <option value="">Selecione...</option>
                        <option value="240">240</option>
                        <option value="400">400</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Reiniciar remessa</label>
                    <select class="form-control form-control-sm reiniciar" name="agreement_reiniciar[]" required>
                        <option value="">Selecione...</option>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2 numero_remessa d-none">
                <div class="form-group">
                    <label>Número da remessa</label>
                    <input type="text" name="agreement_numero_remessa[]" class="form-control form-control-sm remessa" value="">
                </div>
            </div>
            <div class="col-12 col-sm-2">
                <div class="form-group">
                    <label>Transmissão automática</label>
                    <select class="form-control form-control-sm" name="agreement_utiliza_van[]" required>
                        <option value="">Selecione...</option>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-2 div_densidade d-none">
                <div class="form-group">
                    <label>Densidade de remessa</label>
                    <select class="form-control form-control-sm densidade_remessa" name="agreement_densidade_remessa[]">
                        <option value="">Selecione...</option>
                        <option value="1600">1600</option>
                        <option value="6250">6250</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-4">
                <div class="form-group">
                    <label>Nosso Número controlado pelo banco?</label>
                    <select class="form-control form-control-sm" name="agreement_nosso_numero_banco[]" required>
                        <option value="">Selecione...</option>
                        <option value="1">Sim</option>
                        <option value="0">Não</option>
                    </select>
                </div>
            </div>
            <div class="col-12 col-sm-3">
                <div class="form-group">
                    <label>Início Nosso Número</label>
                    <input type="text" name="owner_inicio_nosso_numero[]" class="form-control form-control-sm">
                </div>
            </div>
        </div>
    </div>
</div>