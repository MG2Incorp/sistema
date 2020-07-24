<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use PDF;
use Auth;

use App\ProposalHistoryStatus;

class Proposal extends Model{

    /*

        STATUS:

        0 - Reservado
        1 - Proposta
        2 - Proposta em Análise
        3 - Documentação em Análise
        4 - Emissão de Contrato
        5 - Contrato Disponível
        6 - Em Assinatura
        7 - Vendido
        8 - Reprovado
        9 - Cancelado

    */

    use SoftDeletes;

    protected $table = 'proposals';

    protected $fillable = [
        'property_id', 'user_id', 'media', 'reason', 'notes', 'modality', 'status', 'correction_type', 'correction_index', 'file', 'discount', 'tax'
    ];

    public function proponents() {
        return $this->hasMany('App\Proponent', 'proposal_id')->where('proponent_id', null);
    }

    public function all_proponents() {
        return $this->hasMany('App\Proponent', 'proposal_id');
    }

    public function payments() {
        return $this->hasMany('App\Payment', 'proposal_id');
    }

    public function statuses() {
        return $this->hasMany('App\ProposalHistoryStatus', 'proposal_id');
    }

    public function main_proponent() {
        return $this->hasOne('App\Proponent', 'proposal_id')->where('main', 1);
    }

    public function property() {
        return $this->belongsTo('App\Property', 'property_id');
    }

    public function monetary_index() {
        return $this->belongsTo('App\MonetaryCorrectionIndex', 'correction_index');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id');
    }

    public function contract() {
        return $this->belongsTo('App\Contract', 'modality');
    }

    public function documents() {
        return $this->hasMany('App\Document', 'proposal_id')->where('type', '!=', 'other');
    }

    public function documents_attach() {
        return $this->hasMany('App\Document', 'proposal_id')->where('type', 'other');
    }

    public function billings() {
        return $this->hasManyThrough('App\Billing', 'App\Payment', 'proposal_id', 'payment_id', 'id', 'id');
    }

    public function generateContract() {
        $variables = array();

        $variables['{%BLOCO%}'] = $this->property->block->building->name;
        $variables['{%ANDAR%}'] = $this->property->block->label;
        $variables['{%NUMERO%}'] = $this->property->number;

        $valor_imovel = $this->property->value - $this->discount;

        $variables['{%VALOR%}'] = formatMoney($valor_imovel);
        $variables['{%VALOR_EXTENSO%}'] = convert_number_to_words($valor_imovel);

        $variables['{%AREA%}'] = formatMoney($this->property->size);
        $variables['{%DIMENSOES%}'] = $this->property->dimensions;
        $variables['{%NUMERO_MATRICULA%}'] = $this->property->numero_matricula;
        $variables['{%CADASTRO_IMOBILIARIO%}'] = $this->property->cadastro_imobiliario;

        $variables['{%RAZAO_SOCIAL%}'] = $this->property->block->building->project->social_name;
        $variables['{%CNPJ_EMPREENDIMENTO%}'] = $this->property->block->building->project->cnpj;
        $variables['{%NOME_EMPREENDIMENTO%}'] = $this->property->block->building->project->name;
        $variables['{%DATA_ENTREGA%}'] = formatData($this->property->block->building->project->finish_at);
        $variables['{%STATUS%}'] = $this->property->block->building->project->status;
        $variables['{%LOCAL%}'] = $this->property->block->building->project->local;
        $variables['{%TIPO%}'] = $this->property->block->building->project->type;
        $variables['{%OBSERVACOES%}'] = $this->property->block->building->project->notes;

        $variables['{%COMISSAO%}'] = formatMoney($this->property->block->building->project->comission);
        $variables['{%COMISSAO_EXTENSO%}'] = convert_number_to_words($this->property->block->building->project->comission);
        $variables['{%JUROS%}'] = formatMoney($this->property->block->building->project->fee);
        $variables['{%JUROS_EXTENSO%}'] = convert_number_to_words($this->property->block->building->project->fee);

        $variables['{%DATA_CONTRATO%}'] = formatData(date('Y-m-d'));

        setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
        $variables['{%DATA_CONTRATO_EXTENSO%}'] = utf8_encode(strftime('%d de %B de %Y', strtotime('today')));

        $variables['{%VALOR_PROPOSTA%}'] = formatMoney($this->payments->sum('total_value'));
        $variables['{%VALOR_PROPOSTA_EXTENSO%}'] = convert_number_to_words($this->payments->sum('total_value'));

        $variables['{%NUMERO_PROPOSTA%}'] = $this->id;
        $variables['{%MIDIA%}'] = $this->media;
        $variables['{%MOTIVO%}'] = $this->reason;
        $variables['{%PROPOSTA_OBSERVACOES%}'] = $this->notes;
        $variables['{%MODALIDADE%}'] = $this->modality;
        $variables['{%INDICE_FREQUENCIA%}'] = $this->correction_type;
        $variables['{%INDICE%}'] = $this->correction_index;
        $variables['{%VALOR_DESCONTO%}'] = formatMoney($this->discount);
        $variables['{%VALOR_DESCONTO_EXTENSO%}'] = convert_number_to_words($this->discount);

        $valor_corretagem = (($this->property->value - $this->discount) * $this->property->block->building->project->comission) / 100;

        $variables['{%VALOR_CORRETAGEM%}'] = formatMoney($valor_corretagem);
        $variables['{%VALOR_CORRETAGEM_EXTENSO%}'] = convert_number_to_words($valor_corretagem);

        $company = $corretor = null;
        if($this->user_id) {
            $corretor = \App\User::find($this->user_id);
            $user_project = \App\UserProject::where('user_id', $this->user_id)->where('project_id', $this->property->block->building->project_id)->first();
            if($user_project && $user_project->company_id) {
                $company = \App\Company::find($user_project->company_id);
            }
        }

        $variables['{%IMOBILIARIA_NOME%}'] = $company ? $company->name : '';
        $variables['{%IMOBILIARIA_CRECI%}'] = $company ? $company->creci : '';
        $variables['{%IMOBILIARIA_CNPJ%}'] = $company ? $company->cnpj : '';
        $variables['{%IMOBILIARIA_CORRETOR_NOME%}'] = $corretor ? $corretor->name : '';
        $variables['{%IMOBILIARIA_CORRETOR_CRECI%}'] = $corretor ? $corretor->creci : '';

        $variables['{%TABELA_COMISSAO%}'] = formatMoney($valor_imovel - $valor_corretagem);
        $variables['{%PROPOSTA_COMISSAO%}'] = formatMoney($this->payments->sum('total_value') - $valor_corretagem);

        foreach ($this->proponents as $i => $proponent) {
            $key = $i+1;

            $variables['{%PRINCIPAL-'.$key.'%}'] = $proponent->main;
            $variables['{%TIPO_PESSOA-'.$key.'%}'] = $proponent->type;
            $variables['{%DOCUMENTO-'.$key.'%}'] = $proponent->document;
            $variables['{%RG-'.$key.'%}'] = $proponent->rg;
            $variables['{%RG_EMISSOR-'.$key.'%}'] = $proponent->emitter;
            $variables['{%RG_UF-'.$key.'%}'] = $proponent->rg_state;
            $variables['{%PROPORCAO-'.$key.'%}'] = formatMoney($proponent->proportion);
            $variables['{%NOME-'.$key.'%}'] = $proponent->name;
            $variables['{%EMAIL-'.$key.'%}'] = $proponent->email;
            $variables['{%SEXO-'.$key.'%}'] = $proponent->gender;
            $variables['{%NASCIMENTO-'.$key.'%}'] = formatData($proponent->birthdate);
            $variables['{%TELEFONE-'.$key.'%}'] = $proponent->phone;
            $variables['{%CELULAR-'.$key.'%}'] = $proponent->cellphone;
            $variables['{%MAE-'.$key.'%}'] = $proponent->mother_name;
            $variables['{%PAI-'.$key.'%}'] = $proponent->father_name;
            $variables['{%NATURALIDADE-'.$key.'%}'] = $proponent->birthplace;
            $variables['{%PAIS-'.$key.'%}'] = $proponent->country;
            $variables['{%MORADIA-'.$key.'%}'] = $proponent->house;
            $variables['{%RENDA_BRUTA-'.$key.'%}'] = formatMoney($proponent->gross_income);
            $variables['{%RENDA_LIQUIDA-'.$key.'%}'] = formatMoney($proponent->net_income);
            $variables['{%PROFISSAO-'.$key.'%}'] = $proponent->occupation;
            $variables['{%CARTORIO-'.$key.'%}'] = $proponent->registry;
            $variables['{%ESTADO_CIVIL-'.$key.'%}'] = $proponent->civil_status;
            $variables['{%REGIME_CASAMENTO-'.$key.'%}'] = $proponent->marriage;
            $variables['{%EMPRESA-'.$key.'%}'] = $proponent->company;
            $variables['{%CNPJ-'.$key.'%}'] = $proponent->company_document;
            $variables['{%CARGO-'.$key.'%}'] = $proponent->role;
            $variables['{%ADMISSAO-'.$key.'%}'] = formatData($proponent->hired_at);
            $variables['{%TELEFONE_EMPRESA-'.$key.'%}'] = $proponent->company_phone;
            $variables['{%CELULAR_EMPRESA-'.$key.'%}'] = $proponent->company_cellphone;

            if ($proponent->company_address) {
                $variables['{%CEP_EMPRESA-'.$key.'%}'] = $proponent->company_address->zipcode;
                $variables['{%LOGRADOURO_EMPRESA-'.$key.'%}'] = $proponent->company_address->street;
                $variables['{%NUMERO_EMPRESA-'.$key.'%}'] = $proponent->company_address->number;
                $variables['{%COMPLEMENTO_EMPRESA-'.$key.'%}'] = $proponent->company_address->complement;
                $variables['{%BAIRRO_EMPRESA-'.$key.'%}'] = $proponent->company_address->district;
                $variables['{%CIDADE_EMPRESA-'.$key.'%}'] = $proponent->company_address->city;
                $variables['{%UF_EMPRESA-'.$key.'%}'] = $proponent->company_address->state;
            }

            if ($proponent->address) {
                $variables['{%CEP_RESIDENCIAL-'.$key.'%}'] = $proponent->address->zipcode;
                $variables['{%LOGRADOURO_RESIDENCIAL-'.$key.'%}'] = $proponent->address->street;
                $variables['{%NUMERO_RESIDENCIAL-'.$key.'%}'] = $proponent->address->number;
                $variables['{%COMPLEMENTO_RESIDENCIAL-'.$key.'%}'] = $proponent->address->complement;
                $variables['{%BAIRRO_RESIDENCIAL-'.$key.'%}'] = $proponent->address->district;
                $variables['{%CIDADE_RESIDENCIAL-'.$key.'%}'] = $proponent->address->city;
                $variables['{%UF_RESIDENCIAL-'.$key.'%}'] = $proponent->address->state;
                $variables['{%MESMO_ENDERECO_COBRANCA-'.$key.'%}'] = $proponent->address->is_billing;
            }

            if ($proponent->proponent) {
                $variables['{%CONJUGE_DOCUMENTO-'.$key.'%}'] = $proponent->proponent->document;
                $variables['{%CONJUGE_RG-'.$key.'%}'] = $proponent->proponent->rg;
                $variables['{%CONJUGE_RG_EMISSOR-'.$key.'%}'] = $proponent->proponent->emitter;
                $variables['{%CONJUGE_RG_UF-'.$key.'%}'] = $proponent->proponent->rg_state;
                $variables['{%CONJUGE_PROPORCAO-'.$key.'%}'] = formatMoney($proponent->proponent->proportion);
                $variables['{%CONJUGE_NOME-'.$key.'%}'] = $proponent->proponent->name;
                $variables['{%CONJUGE_EMAIL-'.$key.'%}'] = $proponent->proponent->email;
                $variables['{%CONJUGE_SEXO-'.$key.'%}'] = $proponent->proponent->gender;
                $variables['{%CONJUGE_NASCIMENTO-'.$key.'%}'] = formatData($proponent->proponent->birthdate);
                $variables['{%CONJUGE_TELEFONE-'.$key.'%}'] = $proponent->proponent->phone;
                $variables['{%CONJUGE_CELULAR-'.$key.'%}'] = $proponent->proponent->cellphone;
                $variables['{%CONJUGE_MAE-'.$key.'%}'] = $proponent->proponent->mother_name;
                $variables['{%CONJUGE_PAI-'.$key.'%}'] = $proponent->proponent->father_name;
                $variables['{%CONJUGE_NATURALIDADE-'.$key.'%}'] = $proponent->proponent->birthplace;
                $variables['{%CONJUGE_PAIS-'.$key.'%}'] = $proponent->proponent->country;
                $variables['{%CONJUGE_RENDA_BRUTA-'.$key.'%}'] = formatMoney($proponent->proponent->gross_income);
                $variables['{%CONJUGE_RENDA_LIQUIDA-'.$key.'%}'] = formatMoney($proponent->proponent->net_income);
                $variables['{%CONJUGE_PROFISSAO-'.$key.'%}'] = $proponent->proponent->occupation;
                $variables['{%CONJUGE_CARTORIO-'.$key.'%}'] = $proponent->proponent->registry;
                $variables['{%CONJUGE_EMPRESA-'.$key.'%}'] = $proponent->proponent->company;
                $variables['{%CONJUGE_CNPJ-'.$key.'%}'] = $proponent->proponent->company_document;
                $variables['{%CONJUGE_CARGO-'.$key.'%}'] = $proponent->proponent->role;
                $variables['{%CONJUGE_ADMISSAO-'.$key.'%}'] = $proponent->proponent->hired_at;
                $variables['{%CONJUGE_TELEFONE_EMPRESA-'.$key.'%}'] = $proponent->proponent->phone;
                $variables['{%CONJUGE_CELULAR_EMPRESA-'.$key.'%}'] = $proponent->proponent->cellphone;

                if ($proponent->proponent->company_address) {
                    $variables['{%CONJUGE_CEP_EMPRESA-'.$key.'%}'] = $proponent->proponent->company_address->zipcode;
                    $variables['{%CONJUGE_LOGRADOURO_EMPRESA-'.$key.'%}'] = $proponent->proponent->company_address->street;
                    $variables['{%CONJUGE_NUMERO_EMPRESA-'.$key.'%}'] = $proponent->proponent->company_address->number;
                    $variables['{%CONJUGE_COMPLEMENTO_EMPRESA-'.$key.'%}'] = $proponent->proponent->company_address->complement;
                    $variables['{%CONJUGE_BAIRRO_EMPRESA-'.$key.'%}'] = $proponent->proponent->company_address->district;
                    $variables['{%CONJUGE_CIDADE_EMPRESA-'.$key.'%}'] = $proponent->proponent->company_address->city;
                    $variables['{%CONJUGE_UF_EMPRESA-'.$key.'%}'] = $proponent->proponent->company_address->state;
                }
            }
        }

        $h = '';
        if ($this->payments->count()) {
            $h .= ' <table style="border-collapse: collapse;border: 1px solid black; width: 100%">
                        <tbody>
                            <tr>
                                <td style="border: 1px solid black;padding: 4px" class="text-center"><b>Qtd Parcelas</b></td>
                                <td style="border: 1px solid black;padding: 4px" class="text-center"><b>Tipo</b></td>
                                <td style="border: 1px solid black;padding: 4px" class="text-center"><b>Venc 1ª Parcela</b></td>
                                <td style="border: 1px solid black;padding: 4px" class="text-right"><b>Valor da Parcela</b></td>
                                <td style="border: 1px solid black;padding: 4px" class="text-center"><b>%</b></td>
                                <td style="border: 1px solid black;padding: 4px" class="text-right"><b>Total</b></td>
                            </tr>';

            foreach ($this->payments as $key => $payment) {
                $h .= ' <tr>
                            <td style="border: 1px solid black;padding: 4px" class="text-center">'.$payment->quantity.'</td>
                            <td style="border: 1px solid black;padding: 4px">'.$payment->component.'-'.$payment->method.'</td>
                            <td style="border: 1px solid black;padding: 4px" class="text-center">'.formatData($payment->expires_at).'</td>
                            <td style="border: 1px solid black;padding: 4px" class="text-right">R$ '.formatMoney($payment->unit_value).'</td>
                            <td style="border: 1px solid black;padding: 4px" class="text-center">'.$payment->percentage.' %</td>
                            <td style="border: 1px solid black;padding: 4px" class="text-right">R$ '.formatMoney($payment->total_value).'</td>
                        </tr>';
            }

            $h .= '     </tbody>
                    </table>';
        }

        $variables['{%PAGAMENTOS%}'] = $h;

        $content = str_replace(array_keys($variables), array_values($variables), $this->contract->content);

        for ($i = 4; $i > count($this->proponents); $i--) {
            $content = preg_replace('/{%INICIO_PROPO_'.$i.'%}[\s\S]+?{%FIM_PROPO_'.$i.'%}/', '', $content);
        }

        // for ($i = 4; $i > count($this->proponents); $i--) {
        //foreach ($this->proponents as $i => $proponent) {
        for($i = 0; $i < 4; $i++) {
            //$key = $i+1;

            if (!$this->proponents->get($i) || $this->proponents->get($i) && !$this->proponents->get($i)->proponent) {
            //if ($key > count($this->proponents)) {
                $key = $i+1;
                $content = preg_replace('/{%INICIO_CONJUGE_'.$key.'%}[\s\S]+?{%FIM_CONJUGE_'.$key.'%}/', '', $content);
            }
        }

        //foreach ($this->proponents as $i => $proponent) {
        for($key = 1; $key < 5; $key++) {
            //$key = $i+1;

            $content = preg_replace('/{%INICIO_PROPO_'.$key.'%}/', '', $content);
            $content = preg_replace('/{%FIM_PROPO_'.$key.'%}/', '', $content);

            $content = preg_replace('/{%INICIO_CONJUGE_'.$key.'%}/', '', $content);
            $content = preg_replace('/{%FIM_CONJUGE_'.$key.'%}/', '', $content);
        }

        // \Log::info('CONTENT: '.$content);

        $name = md5(uniqid(rand(), true)).'.pdf';
        $pdf = PDF::loadView('pdf.proposal_contract', ['content' => $content])->save(storage_path('app/public').'/'.$name);

        if ($pdf) {
            $this->update([
                'file' => $name
            ]);
        }
    }

    public function setStatus($status, $notes = null) {
        $this->status = $status;
        $this->save();

        $history = ProposalHistoryStatus::create([
            'proposal_id' => $this->id,
            'status' => $status,
            'user_id' => Auth::user()->id,
            'notes' => $notes
        ]);
    }

    public function getCurrentIndex($months, $hoje) {
        if($this->correction_index) {
            // printa("AQUI");
            $reajuste = \App\MonetaryCorrectionIndexHistory::where('indexes_id', $this->correction_index)->where('month', $hoje->copy()->subMonth()->month)->where('year', $hoje->copy()->subMonth()->year)->first();
            // printa($reajuste);
            if(!$reajuste) return -1;
            if(!$reajuste->value) return -1;

            return ($reajuste->value < 0 ? 0 : $reajuste->value);
        }

        return 0;
    }

    public function generateBilling($months, $indice_atual, $hoje) {
        foreach ($this->payments as $key => $payment) {
            // \Log::info('A');

            //\Log::info('TRY GENERATE BILLING BUTTON PROPOSAL A');

            $primeira_parcela = \Carbon\Carbon::parse($payment->expires_at);

            switch ($payment->component) {
                case 'Anual':           $param = 12;    break;
                case 'Semestral':       $param = 6;     break;
                case 'Trimestral':      $param = 3;     break;
                case 'Bimestral':       $param = 2;     break;
                case 'Mensal':
                case 'Entrada/Sinal':   $param = 1;     break;
                default:                $param = 1;     break;
            }

            // printa('PARAM: '.$param);

            $num_ciclos = ceil(($payment->quantity * $param) / $months);

            // printa("NUM CICLOS: ".$num_ciclos);

            $payments_array = [];
            for ($i = 0; $i < $payment->quantity; $i++) $payments_array[] = $i;
            $ciclos = collect($payments_array)->split($num_ciclos)->toArray();

            // printa('CEIL: '.ceil($payment->quantity / $months));
            // printa('MONTHS: '.$months);

            $meses_com_reajustes = [];
            // for ($i = 0; $i <= ceil($payment->quantity / $months); $i++) {
            for ($i = 0; $i < ceil($payment->quantity * $param / $months); $i++) {
                $aux = $primeira_parcela->copy();
                $parcela = $aux->addMonths($months*$i);
                // printa("PARCELA: ".$aux);
                $meses_com_reajustes[] = (string) $parcela->month."-".$parcela->year;
            }

            // printa("ID PAYMENT: ".$payment->id);
            // printa($meses_com_reajustes);

            // continue;

            for ($i = 0; $i < $payment->quantity; $i++) {
                // \Log::info('B');
                $aux = $primeira_parcela->copy();
                $parcela = $aux->addMonths($param*$i);

                if($parcela < $hoje) continue;

                //\Log::info('TRY GENERATE BILLING BUTTON PROPOSAL B');

                // if(!in_array((string) $hoje->month."-".$hoje->year, $meses_com_reajustes)) continue;

                $valor_parcela_aux = $payment->unit_value;
                $pos_mes_reajuste_anterior = $history = $pos = null;
                // if(!in_array((string) $hoje->month."-".$hoje->year, $meses_com_reajustes)) { /* COMENTEI ESSA LINHA PQ TÀ ESTRANHA1 */
                if(in_array((string) $hoje->month."-".$hoje->year, $meses_com_reajustes)) {
                    // echo "A";
                    $pos = array_search((string) $hoje->month."-".$hoje->year, $meses_com_reajustes);

                    $pos_mes_reajuste_anterior = $pos - 1;
                    if($pos_mes_reajuste_anterior >= 0) {
                        $explode = explode('-', $meses_com_reajustes[$pos_mes_reajuste_anterior]);
                        $history = \App\PaymentInstallmentHistory::where([ 'payment_id' => $payment->id, 'month' => $explode[0], 'year' => $explode[1] ])->first();
                        if(!$history) {
                            //echo "C";
                            $valor_parcela_aux = $payment->unit_value;
                        } else {
                            //echo "D";
                            $valor_parcela_aux = $history->value;
                        }

                        $valor_parcela = $valor_parcela_aux + (($valor_parcela_aux * $indice_atual) / 100);
                    } else {
                        //echo "ALTER CD";
                        $valor_parcela = $valor_parcela_aux;
                    }
                } else {
                    //echo "B";
                    $history = \App\PaymentInstallmentHistory::where([ 'payment_id' => $payment->id ])->orderBy('created_at', 'DESC')->first();
                    if(!$history) {
                        //echo "E";
                        $valor_parcela_aux = $payment->unit_value;
                    } else {
                        //echo "F";
                        $pos = $history->id;
                        $valor_parcela_aux = $history->value;
                    }

                    $valor_parcela = $valor_parcela_aux;
                }

                // printa($valor_parcela);
                // return;

                $billing = \App\Billing::where([ 'payment_id' => $payment->id, 'month' => $parcela->month, 'year' => $parcela->year ])->first();
                if(!$billing) {
                    $billing = \App\Billing::create([
                        'payment_id'    => $payment->id,
                        'month'         => $parcela->month,
                        'year'          => $parcela->year,
                        'expires_at'    => $parcela,
                        'token'         => getBillingToken(8),
                        'value'         => $payment->unit_value,
                        'status'        => 'PENDING'
                    ]);

                    $billing->setStatus('PENDING');
                }

                //\Log::info('TRY GENERATE BILLING BUTTON PROPOSAL C');

                //$valor_parcela = in_array($i, $ciclos[0]) || !$pos ? $payment->unit_value : $valor_parcela_aux + (($valor_parcela_aux * $indice_atual) / 100);

                // printa($valor_parcela);
                // continue;

                if($indice_atual) {
                    if(!in_array($billing->status, [ 'PAID', 'PAID_MANUAL', 'CANCELED' ])) $billing->update([ 'value' => $valor_parcela ]);

                    \App\PaymentInstallmentHistory::updateOrCreate(
                        [ 'payment_id' => $payment->id, 'month' => $hoje->copy()->month, 'year' => $hoje->copy()->year ],
                        [ 'value' => $valor_parcela ]
                    );
                } else {
                    if($pos_mes_reajuste_anterior >= 0 && $history) {
                        \App\PaymentInstallmentHistory::updateOrCreate(
                            [ 'payment_id' => $payment->id, 'month' => $hoje->copy()->month, 'year' => $hoje->copy()->year ],
                            [ 'value' => $history->value ]
                        );
                    } else {
                        \App\PaymentInstallmentHistory::updateOrCreate(
                            [ 'payment_id' => $payment->id, 'month' => $hoje->copy()->month, 'year' => $hoje->copy()->year ],
                            [ 'value' => $payment->unit_value ]
                        );
                    }
                }
            }
        }
    }

    public function generateBillets($hoje) {
        foreach ($this->payments as $key => $payment) {
            if($payment->method != 'Boleto') continue;

            // printa("A");

            if(!$billing = \App\Billing::where('payment_id', $payment->id)->where('expires_at', '>=', $hoje->copy()->startOfDay())->where('month', $hoje->copy()->month)->where('year', $hoje->copy()->year)->first()) continue;

            // printa("B");

            // continue;

            $plugboleto = new \App\Api\PlugBoleto();

            if($billing->billet_generated()) {
                // printa("C"); continue;

                $billet = $billing->billet_generated();
                $boleto = $plugboleto->getBoletos($billet);

                if(isset($boleto->_dados[0]->IdIntegracao) && $boleto->_dados[0]->IdIntegracao == $billet->idIntegracao) {
                    $billet->update([
                        'status'    => isset($boleto->_dados[0]->situacao) ? $boleto->_dados[0]->situacao : null,
                        'bar_code'  => isset($boleto->_dados[0]->TituloLinhaDigitavel) ? $boleto->_dados[0]->TituloLinhaDigitavel : null
                    ]);

                    if(isset($boleto->_dados[0]->motivo) && $boleto->_dados[0]->motivo) {
                        \App\BilletError::create([
                            'billet_id' => $billet->id,
                            'message'   => $boleto->_dados[0]->motivo
                        ]);
                    }
                }
            } else {
                // printa("D"); continue;

                $billet = \App\Billet::create([
                    'billetable_type'   => 'App\Billing',
                    'billetable_id'     => $billing->id,
                    'token'             => getBillingToken(8)
                ]);

                $boleto = $plugboleto->postBoleto($billing, $billet);
                \Log::info('serialize:'.serialize($boleto));

                // printa($boleto);

                // printa("E");

                if(isset($boleto->_dados->_falha[0]->_erro->erros)) {
                    \App\BilletError::create([
                        'billet_id' => $billet->id,
                        'message'   => json_encode($boleto->_dados->_falha[0]->_erro->erros)
                    ]);
                }

                if(isset($boleto->_dados->_sucesso[0]->idintegracao)) {
                    $billet->update([
                        'idIntegracao'  => $boleto->_dados->_sucesso[0]->idintegracao,
                        'emitted_at'    => \Carbon\Carbon::now()->toDateString(),
                        'status'        => isset($boleto->_dados->_sucesso[0]->situacao) ? $boleto->_dados->_sucesso[0]->situacao : null,
                    ]);

                    if(isset($boleto->_dados->_sucesso[0]->situacao)) $billet->setStatus($boleto->_dados->_sucesso[0]->situacao);
                }
            }
        }
    }

    public function generateBilletsGroup($months, $hoje) {
        foreach ($this->payments as $key => $payment) {
            try {
                if($payment->method != 'Boleto') continue;

                $primeira_parcela = \Carbon\Carbon::parse($payment->expires_at);

                switch ($payment->component) {
                    case 'Anual':           $param = 12;    break;
                    case 'Semestral':       $param = 6;     break;
                    case 'Trimestral':      $param = 3;     break;
                    case 'Bimestral':       $param = 2;     break;
                    case 'Mensal':
                    case 'Entrada/Sinal':   $param = 1;     break;
                    default:                $param = 1;     break;
                }

                /* PEGA OS MESES QUE TEM REAJUSTE PARA ENVIAR OS BOLETOS */
                $meses_com_reajustes = [];
                // for ($i = 0; $i <= ceil($payment->quantity / $months); $i++) {
                for ($i = 0; $i < ceil($payment->quantity * $param / $months); $i++) {
                    $aux = $primeira_parcela->copy();
                    $parcela = $aux->addMonths($months*$i);
                    $meses_com_reajustes[] = (string) $parcela->month."-".$parcela->year;
                }

                //printa($meses_com_reajustes);
                // continue;

                //\Log::info('TRY GENERATE BILLING BUTTON BILLETS GROUP ANTES CHECK MES REAJUSTE');

                //\Log::info('MESES COM REAJUSTE: '.serialize($meses_com_reajustes));

                // printa($meses_com_reajustes);

                // continue;

                // echo (string) $hoje->month."-".$hoje->year;

                if(in_array((string) $hoje->month."-".$hoje->year, $meses_com_reajustes)) {
                    //printa("IF"); //continue;
                    //\Log::info('IF - TRY GENERATE BILLING BUTTON BILLETS GROUP DEPOIS CHECK MES REAJUSTE');

                    $pos = array_search((string) $hoje->month."-".$hoje->year, $meses_com_reajustes);
                    /* PEGA O PRÓXIMO MÊS COM REAJUSTE QUE VAI SER O LIMITE PARA ENVIAR OS BOLETOS */
                    $end = $pos + 1;

                    $prazo_inicial = \Carbon\Carbon::createFromDate($hoje->year, $hoje->month, null)->startOfMonth();

                    if(isset($meses_com_reajustes[$end])) {
                        $explode = explode('-', $meses_com_reajustes[$end]);
                        $prazo_final = \Carbon\Carbon::createFromDate($explode[1], $explode[0], null)->subMonth()->endOfMonth();
                        $billings = \App\Billing::where('payment_id', $payment->id)->whereBetween('expires_at', [ $prazo_inicial, $prazo_final ])->get();
                    } else {
                        $billings = \App\Billing::where('payment_id', $payment->id)->where('expires_at', '>', $prazo_inicial)->get();
                    }
                } else {
                    //printa("ELSE"); //continue;
                    //\Log::info('ELSE - TRY GENERATE BILLING BUTTON BILLETS GROUP DEPOIS CHECK MES REAJUSTE');

                    $prazo_inicial = \Carbon\Carbon::createFromDate($hoje->year, $hoje->month, null)->startOfMonth();

                    $proximo_reajuste = null;
                    for ($i = 1; $i < 13; $i++) {
                        $aux = $prazo_inicial->copy();
                        $parcela = $aux->addMonths($i);

                        if(in_array((string) $parcela->month."-".$parcela->year, $meses_com_reajustes)) {
                            $proximo_reajuste = array_search((string) $parcela->month."-".$parcela->year, $meses_com_reajustes);
                            break;
                        }
                    }

                    // printa($proximo_reajuste);

                    if($proximo_reajuste && isset($meses_com_reajustes[$proximo_reajuste])) {
                        $explode = explode('-', $meses_com_reajustes[$proximo_reajuste]);
                        $prazo_final = \Carbon\Carbon::createFromDate($explode[1], $explode[0], null)->subMonth()->endOfMonth();
                        $billings = \App\Billing::where('payment_id', $payment->id)->whereBetween('expires_at', [ $prazo_inicial, $prazo_final ])->get();
                    } else {
                        continue;
                    }
                }

                // printa($billings->pluck('expires_at')->toArray());
                // continue;

                // /* SE NÃO FOR MÊS COM REAJUSTE NÃO FAZ NADA */
                // if(!in_array((string) $hoje->month."-".$hoje->year, $meses_com_reajustes)) continue;

                // \Log::info('TRY GENERATE BILLING BUTTON BILLETS GROUP DEPOIS CHECK MES REAJUSTE');

                // $pos = array_search((string) $hoje->month."-".$hoje->year, $meses_com_reajustes);
                // /* PEGA O PRÓXIMO MÊS COM REAJUSTE QUE VAI SER O LIMITE PARA ENVIAR OS BOLETOS */
                // $end = $pos + 1;

                // $prazo_inicial = \Carbon\Carbon::createFromDate($hoje->year, $hoje->month, null)->startOfMonth();

                // if(isset($meses_com_reajustes[$end])) {
                //     $explode = explode('-', $meses_com_reajustes[$end]);
                //     $prazo_final = \Carbon\Carbon::createFromDate($explode[1], $explode[0], null)->subMonth()->endOfMonth();
                //     $billings = \App\Billing::where('payment_id', $payment->id)->whereBetween('expires_at', [ $prazo_inicial, $prazo_final ])->get();
                // } else {
                //     $billings = \App\Billing::where('payment_id', $payment->id)->where('expires_at', '>', $prazo_inicial)->get();
                // }

                if($billings->count()) {
                    foreach($billings as $key => $billing) {
                        // printa($billing->expires_at);
                        // continue;

                        //\Log::info('TRY CREATE BILLET FOR BILLING: '.$billing->id);

                        $plugboleto = new \App\Api\PlugBoleto();

                        /* SE AINDA NÂO EXISTE O BOLETO DA COBRANÇA, TENTA GERAR */
                        if(!$billing->billet_generated()) {
                            $billet = \App\Billet::create([
                                'billetable_type'   => 'App\Billing',
                                'billetable_id'     => $billing->id,
                                'token'             => getBillingToken(8)
                            ]);

                            $boleto = $plugboleto->postBoleto($billing, $billet);
                            \Log::info('serialize:'.serialize($boleto));

                            if(isset($boleto->_dados->_falha[0]->_erro->erros)) {
                                \App\BilletError::create([
                                    'billet_id' => $billet->id,
                                    'message'   => json_encode($boleto->_dados->_falha[0]->_erro->erros)
                                ]);
                            }

                            if(isset($boleto->_dados->_sucesso[0]->idintegracao)) {
                                $billet->update([
                                    'idIntegracao'  => $boleto->_dados->_sucesso[0]->idintegracao,
                                    'emitted_at'    => \Carbon\Carbon::now()->toDateString(),
                                    'status'        => isset($boleto->_dados->_sucesso[0]->situacao) ? $boleto->_dados->_sucesso[0]->situacao : null,
                                ]);

                                if(isset($boleto->_dados->_sucesso[0]->situacao)) $billet->setStatus($boleto->_dados->_sucesso[0]->situacao);
                            }
                        }
                    }
                }
            } catch (\Exception $e) { logging($e); }
        }
    }

    public function generateAmortization() {
        foreach ($this->payments as $key => $payment) {
            $valor_devedor = $payment->billings->sum('value');
            $valor_amortizado = $payment->billings->whereIn('status', [ 'PAID', 'PAID_VALUE' ])->sum('amortization_value');

            // echo "VALOR DEVEDOR NO MOMENTO: R$ ".formatMoney($valor_devedor)."<br>";
            // echo "VALOR AMORTIZADO NO MOMENTO: R$ ".formatMoney($valor_amortizado)."<br>";
            // echo "TAXA DE JUROS: ".$payment->proposal->tax."<br><br>";

            $valor_devedor = $valor_devedor - $valor_amortizado;

            foreach ($payment->billings->whereNotIn('status', [ 'PAID', 'PAID_MANUAL' ]) as $key => $billing) {
                $amortizacao = $billing->value - ($valor_devedor * $payment->proposal->tax) / 100;
                // echo "VALOR PARCELA: R$ ".$billing->value." -- AMORTIZACAO: R$ ".formatMoney($amortizacao)."<br>";

                $billing->update([ 'amortization_value' => $amortizacao ]);

                $valor_devedor = $valor_devedor - $amortizacao;
            }

            // echo "<br><br><br><br><br>";
        }
    }

    public function getContractStatus() {
        $situation = 'ON_DAY';

        if($this->status == 'CANCELED') return $this->status;

        $finalizado = 0;
        foreach ($this->payments as $key => $payment) {

            if($payment->billings->whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->count() == $payment->billings->count() && $payment->billings->count() > 0) $finalizado++;

            if($finalizado == $this->payments->count()) {
                $situation = 'FINISH';
                break;
            }

            foreach ($payment->billings->where('expires_at', '<', \Carbon\Carbon::now()->subDays(3)) as $key => $billing) {
                if(!in_array($billing->status, [ 'PAID', 'PAID_MANUAL' ])) {
                    $situation = 'OVERDUE';
                    continue 2;
                }
            }
        }

        return $situation;
    }

    public function generateAntecipation($payment, $i) {

        $valor_futuro = 0;
        $mensalidade = 470.73;
        $periodo = $i;
        $juros = 0.01;

        $valor_futuro = ( $valor_futuro / pow( ( 1 + $juros ), $periodo ) ) + ( $mensalidade * ( pow( ( 1 + $juros ), $periodo ) - 1 ) ) / ( pow( ( 1 + $juros ), ( $periodo + 1 ) ) - pow( ( 1 + $juros ), $periodo ) );

        $periodo = $payment->billings->count() - $i;
        $mensalidade = 0;

        $valor_presente = ( $valor_futuro / pow( ( 1 + $juros ), $periodo ) ) + ( $mensalidade * ( pow( ( 1 + $juros ), $periodo ) - 1 ) ) / ( pow( ( 1 + $juros ), ( $periodo + 1 ) ) - pow( ( 1 + $juros ), $periodo ) );

        return $valor_presente;
    }

    public function pending_ahead() {
        return \App\Ahead::whereIn('payment_id', $this->payments->pluck('id')->toArray())->where('is_total', 1)->where('status', 'PENDING')->first();
    }
}
