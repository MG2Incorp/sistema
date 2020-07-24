<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OwnerController extends Controller
{
    private $data = array();

    public function index() {
        $this->data['owners'] = \App\Owner::all();
        return view('owners.index', $this->data);
    }

    public function create(Request $request) {
        return view('owners.create', $this->data);
    }

    public function store(Request $request) {

        // printa($request->all());
        // dd();

        $erros = [];

        $data = [
            'alias'         => $request->owner_alias,
            'social_name'   => $request->owner_social_name,
            'name'          => $request->owner_name,
            'document'      => $request->owner_document,
            'logradouro'    => $request->owner_street,
            'numero'        => $request->owner_number,
            'complemento'   => $request->owner_complement,
            'bairro'        => $request->owner_district,
            'cep'           => $request->owner_zipcode,
            'cidade'        => $request->owner_city,
            'cidade_ibge'   => $request->owner_city_ibge,
            'uf'            => $request->owner_uf,
            'telefone'      => $request->owner_phone,
            'email'         => $request->owner_email,
            'status'        => $request->owner_status
        ];

        if($request->has('owner_id')) {
            if(!$owner = \App\Owner::find($request->owner_id)) return redirect()->back()->with('error', 'Não foi possível salvar o proprietário.');
            $owner->update($data);
        } else {
            $owner = \App\Owner::updateOrCreate(
                [ 'document' => $request->owner_document ],
                $data
            );
        }

        if(!$owner) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        $plugboleto = new \App\Api\PlugBoleto();
        $retorno = $plugboleto->getCedente($owner->document);
        if(isset($retorno->_dados[0]->id)) {
            if(isset($retorno->_dados[0]->id)) $owner->update([ 'plugboleto_id' => $retorno->_dados[0]->id ]);
            $retorno = $plugboleto->putCedente($owner);

            if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Cedente'] = @$dado->_campo." - ".$dado->_erro; }
            if(isset($retorno->_dados->id)) $owner->update([ 'plugboleto_id' => $retorno->_dados->id ]);
        } else {
            $retorno = $plugboleto->postCedente($owner);

            if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Cedente'] = @$dado->_campo." - ".$dado->_erro; }
            if(isset($retorno->_dados->id)) $owner->update([ 'plugboleto_id' => $retorno->_dados->id ]);
        }

        if($request->has('old_owner_bank_code')) {
            foreach ($request->old_owner_bank_code as $key => $account) {
                if(!$acc = \App\Account::find($key)) continue;

                $acc->update([
                    'bank_code'             => $request->old_owner_bank_code[$key],
                    'agency'                => $request->old_owner_agency[$key],
                    'agency_dv'             => $request->old_owner_agency_dv[$key],
                    'number'                => $request->old_owner_account_number[$key],
                    'number_dv'             => $request->old_owner_account_number_dv[$key],
                    'type'                  => $request->old_owner_account_type[$key],
                    'beneficiario'          => $request->old_owner_beneficiario[$key],
                    'company_code'          => $request->old_owner_company_code[$key],
                    'inicio_nosso_numero'   => $request->old_owner_inicio_nosso_numero[$key],
                    'status'                => $request->old_owner_account_status[$key]
                ]);

                $retorno = $plugboleto->getConta($acc);
                \Log::info("PROCURA CONTA: ".serialize($retorno));
                if(isset($retorno->_dados[0]->id)) {
                    if(isset($retorno->_dados[0]->id)) $acc->update([ 'plugboleto_id' => $retorno->_dados[0]->id ]);
                    $retorno = $plugboleto->putConta($acc);
                    \Log::info("PUT CONTA: ".serialize($retorno));

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Conta'] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $acc->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                } else {
                    $retorno = $plugboleto->postConta($acc);
                    // \Log::info("POST CONTA: ".serialize($retorno));

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Conta'] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $acc->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                }

                $agreement = \App\Agreement::updateOrCreate(
                    [
                        'account_id' => $acc->id
                    ],
                    [
                        'numero'                 => $request->old_agreement_numero[$key],
                        'descricao'             => $request->old_agreement_descricao[$key],
                        'carteira'              => $request->old_agreement_carteira[$key],
                        'especie'               => 'R$',
                        'cnab'                  => $request->old_agreement_padrao[$key],
                        'reiniciar'             => $request->old_agreement_reiniciar[$key],
                        'numero_remessa'        => $request->old_agreement_numero_remessa[$key],
                        'utiliza_van'           => $request->old_agreement_utiliza_van[$key],
                        'densidade_remessa'     => $request->old_agreement_densidade_remessa[$key],
                        'nosso_numero_banco'    => $request->old_agreement_nosso_numero_banco[$key],
                    ]
                );

                $retorno = $plugboleto->getConvenio($agreement);
                if(isset($retorno->_dados[0]->id)) {
                    if(isset($retorno->_dados[0]->id)) $agreement->update([ 'plugboleto_id' => $retorno->_dados[0]->id ]);
                    $retorno = $plugboleto->putConvenio($agreement);

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Convênio'][] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $agreement->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                } else {
                    $retorno = $plugboleto->postConvenio($agreement);

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Convênio'][] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $agreement->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                }
            }
        }

        if($request->has('owner_bank_code')) {
            for ($i = 0; $i < count($request->owner_bank_code); $i++) {
                $acc = \App\Account::create([
                    'owner_id'              => $owner->id,
                    'bank_code'             => $request->owner_bank_code[$i],
                    'agency'                => $request->owner_agency[$i],
                    'agency_dv'             => $request->owner_agency_dv[$i],
                    'number'                => $request->owner_account_number[$i],
                    'number_dv'             => $request->owner_account_number_dv[$i],
                    'type'                  => $request->owner_account_type[$i],
                    'beneficiario'          => $request->owner_beneficiario[$i],
                    'company_code'          => $request->owner_company_code[$i],
                    'inicio_nosso_numero'   => $request->owner_inicio_nosso_numero[$i],
                    'status'                => $request->owner_account_status[$i]
                ]);

                $retorno = $plugboleto->getConta($acc);
                if(isset($retorno->_dados[0]->id)) {
                    if(isset($retorno->_dados[0]->id)) $acc->update([ 'plugboleto_id' => $retorno->_dados[0]->id ]);
                    $retorno = $plugboleto->putConta($acc);

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Conta'][] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $acc->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                } else {
                    $retorno = $plugboleto->postConta($acc);

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Conta'][] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $acc->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                }

                $agreement = \App\Agreement::create([
                    'account_id'            => $acc->id,
                    'numero'                => $request->agreement_numero[$i],
                    'descricao'             => $request->agreement_descricao[$i],
                    'carteira'              => $request->agreement_carteira[$i],
                    'especie'               => 'R$',
                    'cnab'                  => $request->agreement_padrao[$i],
                    'reiniciar'             => $request->agreement_reiniciar[$i],
                    'numero_remessa'        => $request->agreement_numero_remessa[$i],
                    'utiliza_van'           => $request->agreement_utiliza_van[$i],
                    'densidade_remessa'     => $request->agreement_densidade_remessa[$i],
                    'nosso_numero_banco'    => $request->agreement_nosso_numero_banco[$i],
                ]);

                $retorno = $plugboleto->getConvenio($agreement);
                if(isset($retorno->_dados[0]->id)) {
                    if(isset($retorno->_dados[0]->id)) $agreement->update([ 'plugboleto_id' => $retorno->_dados[0]->id ]);
                    $retorno = $plugboleto->putConvenio($agreement);

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Convênio'][] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $agreement->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                } else {
                    $retorno = $plugboleto->postConvenio($agreement);

                    if(isset($retorno->_status) && $retorno->_status == 'erro' && isset($retorno->_dados) && is_array($retorno->_dados)) { foreach($retorno->_dados as $dado) $erros['Convênio'][] = @$dado->_campo." - ".$dado->_erro; }
                    if(isset($retorno->_dados->id)) $agreement->update([ 'plugboleto_id' => $retorno->_dados->id ]);
                }
            }
        }

        return redirect()->route('owners.edit', [ $owner->id ])->with('erros', $erros);
    }

    public function edit($id) {
        if(!$owner = \App\Owner::find($id)) return redirect()->back()->with('error', 'Proprietário não encontrado.');

        $this->data['owner'] = $owner;

        return view('owners.create', $this->data);
    }

    public function update(Request $request, $id) {
        return redirect()->route('owners.index')->with('success', 'Proprietário editado com sucesso.');
    }

    public function delete($id) {

    }

    public function search(Request $request) {
        if(!$request->has('id') || !is_array($request->id) || !count($request->id)) return '';

        $array = [];
        if($request->has('array')) {
            foreach ($request->array as $key => $arr) {
                $array = array_merge($array, $arr);
            }
        }

        $owners = \App\Owner::whereIn('id', $request->id)->get();

        $h = '';
        foreach ($owners as $key => $owner) {
            $h .= ' <div class="form-group">
                        <label>Selecione as contas do proprietário <b>'.$owner->alias.'</b></label>
                        <select name="select_owner['.$owner->id.'][]" class="form-control js-choice select_account" multiple>';
                foreach ($owner->accounts->where('status', 'ACTIVE') as $key => $account) {
                    $h .= '<option value="'.$account->id.'" '.(in_array($account->id, $array) ? 'selected' : '').'>'.getBankCode($account->bank_code).' | Ag. '.$account->agency.'-'.$account->agency_dv.' | Num. '.$account->number.'-'.$account->number_dv.'</option>';
                }
            $h .= '     </select>
                    </div>';
        }

        return $h;
    }
}