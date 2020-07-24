<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BillingController extends Controller
{
    private $data = array();

    public function index(Request $request) {
        $this->data['breadcrumb'][] = ['text' => 'Financeiro', 'is_link' => 0, 'link' => null];

        $filters = array();
        if ($request->has('number'))    $filters['number'] = $request->number;
        if ($request->has('proponent')) $filters['proponent'] = $request->proponent;
        if ($request->has('status'))    $filters['status'] = $request->status;
        if ($request->has('project'))   $filters['project'] = $request->project;

        $filters = array_filter($filters, 'strlen');
        $this->data['filters'] = $filters;

        $builder = '';
        $status = null;
        if(count($filters)) $builder = '&'.http_build_query($filters);
        $this->data['builder'] = $builder;

        $proposals = \App\Proposal::where('status', 'SOLD');

        foreach ($filters as $key => $filter) {
            switch ($key) {
                case 'number':
                    $proposals = $proposals->where('id', $filter);
                break;
                case 'project':
                    $project = \App\Project::find($filter);
                    $proposals = $proposals->whereIn('property_id', $project->properties->pluck('id')->toArray());
                break;
                case 'proponent':
                    $proposals = $proposals->whereHas('all_proponents', function($query) use ($filter) {
                        $query->where('name', 'LIKE', '%'.$filter.'%');
                    });
                break;
                case 'status':
                    if($filter != 'ALL') $status = $filter;
                break;
            }
        }

        switch (\Auth::user()->role) {
            case 'ADMIN':
                $ids = $proposals->latest()->get()->filter(function($item, $key) use ($status) { return $status ? $item->getContractStatus() == $status : true; })->pluck('id')->toArray();
                $this->data['proposals'] = \App\Proposal::whereIn('id', $ids)->latest()->paginate(20);

                $all_proposals = \App\Proposal::where('status', 'SOLD')->latest()->get();

                $this->data['projects'] = \App\Project::all();
            break;
            case 'INCORPORATOR':
                $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

                $properties_ids = array();
                foreach ($projects as $key => $project) {
                    if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                    $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
                }
                $properties_ids = array_unique($properties_ids);

                $ids = $proposals->whereIn('property_id', $properties_ids)->latest()->get()->filter(function($item, $key) use ($status) { return $status ? $item->getContractStatus() == $status : true; })->pluck('id')->toArray();
                $this->data['proposals'] = \App\Proposal::whereIn('id', $ids)->latest()->paginate(20);

                $all_proposals = \App\Proposal::where('status', 'SOLD')->whereIn('property_id', $properties_ids)->latest()->get();

                $this->data['projects'] = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get()->filter(function($item, $key) {
                    return \Auth::user()->checkPermission($item->id, ['FINANCIAL_MODULE_ACCESS']);
                });
            break;
            default: return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
        }

        /* TESTA SE TE ALGUM INADIMPLENTE */
        $payments = [];
        foreach ($all_proposals as $key => $proposal) {
            $payments = array_merge($payments, $proposal->payments->pluck('id')->toArray());
        }

        $this->data['overdues'] = \App\Billing::whereIn('status', [ 'PENDING', 'OUTDATED' ])->where('expires_at', '<', \Carbon\Carbon::now()->subDays(3))->get()->filter(function($item, $key) use ($payments) {
            return in_array($item->payment_id, $payments);
        });

        return view('billing.index', $this->data);
    }

    public function details($id) {

        $this->data['breadcrumb'][] = ['text' => 'Financeiro', 'is_link' => 0, 'link' => null];

        if(!$proposal = \App\Proposal::where('id', $id)->where('status', 'SOLD')->first()) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');
        }

        $this->data['proposal'] = $proposal;

        // $this->data['billings'] = $billings = \App\Billing::whereIn('payment_id', $proposal->payments->pluck('id')->toArray())->get()->groupBy([ 'year', 'month' ])->sortKeys();
        $this->data['billings'] = $billings = \App\Billing::whereIn('payment_id', $proposal->payments->pluck('id')->toArray())->get()->groupBy('year')->map(function($item, $key) {
            return $item->groupBy('month')->sortKeys();
        })->sortKeys();

        $this->data['aheads'] = $aheads = \App\Billing::whereIn('payment_id', $proposal->payments->pluck('id')->toArray())->get()->groupBy('payment_id')->map(function($item, $key) {
            return $item->groupBy('year')->map(function($item2, $key2) {
                return $item2->groupBy('month')->sortKeys();
            })->sortKeys();
        });

        return view('billing.details', $this->data);
    }

    public function billing(Request $request) {

        if(!$billing = \App\Billing::find($request->id)) return 'Cobrança não encontrada.';

        $h = '';
        if($request->has('client')) {

            if(!$billing->payment->proposal->all_proponents->pluck('client_id')->contains(auth()->guard('client')->user()->id)) return 'Cobrança não encontrada.';

            if($billing->billet_generated()) {
                $h .= ' <table class="table table-sm table-bordered m-0 text-center">
                            <thead>
                                <tr>
                                    <th>Data da Emissão</th>
                                    <th>Token</th>
                                    <th>Status</th>
                                    <th>Pagamento Data</th>
                                    <th>Valor Pago</th>
                                </tr>
                            </thead>
                            <tbody>';
                                if($billing->billet_generated()) {
                                    $billet = $billing->billet_generated();
                                    $h .= ' <tr>
                                                <td>'.formatData($billet->emitted_at).'</td>
                                                <td><a href="'.route('billing.billet').'?billet='.$billet->token.'" target="_BLANK">'.$billet->token.'</a></td>
                                                <td>'.$billet->status.'</td>
                                                <td>'.$billet->PagamentoData.'</td>
                                                <td>'.$billet->PagamentoValorPago.'</td>
                                            </tr>';
                                }
                    $h .=  '</tbody>
                        </table>';
            } else {
                $h .= 'Nenhum boleto encontrado para essa cobrança.';
            }
        } else {

            if (\Auth::user()->role == 'INCORPORATOR') {
                $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

                $properties_ids = array();
                foreach ($projects as $key => $project) {
                    if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                    $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
                }
                $properties_ids = array_unique($properties_ids);

                if(!in_array($billing->payment->proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');
            }

            if($billing->billets->count()) {
                $h .= ' <ul class="nav nav-tabs" role="tablist">
                            <li class="nav-item"><a class="nav-link active" id="home-tab" data-toggle="tab" href="#valid" role="tab" aria-controls="home" aria-selected="true">Válido</a></li>
                            <li class="nav-item"><a class="nav-link" id="profile-tab" data-toggle="tab" href="#invalids" role="tab" aria-controls="invalids" aria-selected="false">Erros</a></li>
                        </ul>
                        <div class="tab-content nav-fill">
                            <div class="tab-pane fade show active" id="valid" role="tabpanel">
                                <div class="card border-top-0">
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered text-center">
                                            <thead>
                                                <tr>
                                                    <th>Data da Emissão</th>
                                                    <th>Id do Boleto</th>
                                                    <th>Status</th>
                                                    <th>Pagamento Data</th>
                                                    <th>Valor Pago</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                                if($billing->billet_generated()) {
                                                    $billet = $billing->billet_generated();

                                                    $h .= ' <tr>
                                                                <td>'.formatData($billet->emitted_at).'</td>
                                                                <td><a href="'.route('billing.billet').'?billet='.$billet->token.'" target="_BLANK">'.$billet->idIntegracao.'</a></td>
                                                                <td>'.$billet->status.'</td>
                                                                <td>'.$billet->PagamentoData.'</td>
                                                                <td>'.$billet->PagamentoValorPago.'</td>
                                                            </tr>';
                                                }
                                    $h .=  '</tbody>
                                        </table>';

                                        if($billing->billet_generated()) {
                                            $billet = $billing->billet_generated();

                                            if($billet->email_sent_protocol) {
                                                $api = new \App\Api\PlugBoleto();
                                                $solicitacao = $api->consultarEnvioEmail($billet);

                                                if(isset($solicitacao->_status)) {
                                                    if ($solicitacao->_status == 'sucesso') {
                                                        if(isset($solicitacao->_dados->situacao)) {
                                                            $h .= '<div>Envio do e-mail ('.$solicitacao->_dados->situacao.'): '.(isset($solicitacao->_dados->_mensagem) ? $solicitacao->_dados->_mensagem : '').'</div>';
                                                        }
                                                    } else {
                                                        if(isset($solicitacao->_mensagem)) $h .= '<div>Envio do e-mail (FALHA): '.$solicitacao->_mensagem.'</div>';
                                                    }
                                                }
                                            }

                                        }

                            $h .= ' </div>
                                </div>
                            </div>
                            <div class="tab-pane fade" id="invalids" role="tabpanel">
                                <div class="card border-top-0">
                                    <div class="card-body">
                                        <table class="table table-sm table-bordered m-0 text-center">
                                            <thead>
                                                <tr>
                                                    <th width="15%">Data da Emissão</th>
                                                    <th>Erros</th>
                                                </tr>
                                            </thead>
                                            <tbody>';
                                                foreach ($billing->billets->whereNotIn('status', [ 'SALVO', 'EMITIDO', 'REGISTRADO', 'LIQUIDADO', 'BAIXADO' ])->sortByDesc('created_at') as $key => $billet) {
                                                    $h .= ' <tr>
                                                                <td>'.formatData($billet->emitted_at).'</td>
                                                                <td>';
                                                                    if($billet->errors->count()) {
                                                                        foreach ($billet->errors as $key => $error) {
                                                                            $h .= '<div>'.$error->message.'</div>';
                                                                        }
                                                                    }
                                                $h .= '        </td>
                                                            </tr>';
                                                }
                                    $h .=  '</tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>';
            } else {
                $h .= 'Nenhum boleto encontrado para essa cobrança.';
            }
        }

        return $h;
    }

    public function billet(Request $request) {

        if(!$billet = \App\Billet::where('token', $request->billet)->first()) return redirect()->back()->with('error', 'Boleto não encontrado.');

        if($billet->billetable_type == 'App\Ahead') {
            if(\Carbon\Carbon::now()->startOfDay() > \Carbon\Carbon::parse($billet->emitted_at)->endOfDay()) return redirect()->back()->with('error', 'Visualização do boleto não disponível no momento.');
        }

        $api = new \App\Api\PlugBoleto();
        // if(!$billet->impressao) {
            \Log::info("ID IMPRESSAO BOLETO: ".$billet->impressao);
            $solicitacao = $api->solicitarPDF($billet);

            if(isset($solicitacao->_dados->protocolo) && $solicitacao->_dados->protocolo) {
                $billet->update([ 'impressao' => $solicitacao->_dados->protocolo ]);
            }
        // }

        do {
            \Log::info("ID IMPRESSAO BOLETO: ".$billet->impressao);
            $file = $api->imprimirPDF($billet);
            $retorno = json_decode($file);

        } while (isset($retorno->_dados[0]->situacao) && $retorno->_dados[0]->situacao == 'PROCESSANDO');

        $response = \Response::make($file, 200);
        $response->header('Content-Type', 'application/pdf');

        return $response;
    }

    public function billet_baixa(Request $request) {
        if(!$billet = \App\Billet::where('token', $request->billet)->first()) return redirect()->back()->with('error', 'Boleto não encontrado.');

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($billet->billetable->payment->proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');
        }

        $api = new \App\Api\PlugBoleto();
        $solicitacao = $api->imprimirPDFBaixa($billet);

        if(isset($solicitacao->_dados->_sucesso[0]->remessa)) {
            $decode = base64_decode($solicitacao->_dados->_sucesso[0]->remessa);

            printa($decode);
        }

        printa($solicitacao);
    }

    public function billet_remessa(Request $request) {
        // if(!$billet = \App\Billet::where('token', $request->billet)->first()) return redirect()->back()->with('error', 'Boleto não encontrado.');
        if(!$request->has('billets') || !is_array($request->billets) || !count($request->billets)) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        // if (\Auth::user()->role == 'INCORPORATOR') {
        //     $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

        //     $properties_ids = array();
        //     foreach ($projects as $key => $project) {
        //         $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
        //     }
        //     $properties_ids = array_unique($properties_ids);

        //     if(!in_array($billet->billetable->payment->proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');
        // }

        $billets = \App\Billet::whereIn('id', $request->billets)->get();

        $api = new \App\Api\PlugBoleto();
        $solicitacao = $api->gerarRemessa($billets);

        // printa($solicitacao);
        // return;

        if(isset($solicitacao->_dados->_sucesso[0]->remessa) && strlen($solicitacao->_dados->_sucesso[0]->remessa)) {
            $contents   = base64_decode($solicitacao->_dados->_sucesso[0]->remessa);
            $name       = $solicitacao->_dados->_sucesso[0]->arquivo;
            $path       = public_path($name);

            file_put_contents($path, $contents);

            return response()->download($path)->deleteFileAfterSend(true);
        }

        return redirect()->back()->with('error', 'Não foi possível gerar o arquivo de remessa.');
    }

    public function email_sent(Request $request) {
        if(!$billet = \App\Billet::where('token', $request->billet)->first()) return redirect()->back()->with('error', 'Boleto não encontrado.');

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($billet->billetable->payment->proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');
        }

        $api = new \App\Api\PlugBoleto();
        $solicitacao = $api->solicitarEmail($billet);

        if(isset($solicitacao->_dados->protocolo) && $solicitacao->_dados->protocolo) $billet->update([ 'email_sent_protocol' => $solicitacao->_dados->protocolo ]);

        return redirect()->back();
    }

    public function billet_send_whatsapp(Request $request) {
        if(!$billing = \App\Billing::find($request->id)) return 'Cobrança não encontrada.';

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($billing->payment->proposal->property_id, $properties_ids)) return 'Proposta não encontrada ou inválida.';
        }

        if(!$billing->billet_generated()) return 'Nenhum boleto encontrado para essa cobrança.';

        $billet = $billing->billet_generated();

        $proponent = $billing->payment->proposal->main_proponent;

        $retorno = '';

        try {
            $mailer = new \App\Mailer();
            $send = $mailer->sendMailBilletWP($proponent->cellphone, $billing->payment->proposal->property->block->building->project, $billet);
            $retorno .= '<div class="text-success">Enviar para o número '.$proponent->cellphone.', clique <a href="'.$send.'" target="_BLANK">aqui</a></div>';
        } catch (\Exception $e) {
            $retorno .= '<div class="text-danger">Link para o número '.$proponent->cellphone.' não foi gerado com sucesso.</div>';
            logging($e);
        }

        return $retorno;
    }

    public function generate(Request $request) {

        //if(\Auth::user()->id != 1) return "Em manutenção";

        /*
            CÁLCULO SERÁ FEITO EM CIMA DE UM ÚNICO ÍNDICE, POIS COMO INFORMADO PELO CLIENTE NO DIA 11/09/2019.
            QUANDO FOR EFETUADO UM FINANCIAMENTO PELO BANCO, O MESMO REPASSARÁ O VALOR TOTAL PARA INCORPORADORA E ASSUMIRÁ OS CÁLCULOS DO FINANCIAMENTO EFETUANDO REAJUSTES DIRETAMENTE COM O COMPRADOR/PROPRIETÁRIO DO IMÓVEL.
            RESUMINDO: O COMPRADOR FINANCIA 250 MIL COM O BANCO. O BANCO REPASSA OS 250 MIL À INCORPORADORA E RECEBE POR EXEMPLO 380 MIL DO COMPRADOR APLICANDO JUROS E CORREÇÕES DE CADA PARCELA.
        */

        \Log::info('TRY GENERATE BILLING BEFORE A');

        /* PEGA A DATA DE HOJE PARA COMPARAÇÃO */
        $today = $hoje = \Carbon\Carbon::now()->startOfDay();
        //$today = $hoje = \Carbon\Carbon::create(2019, 11, 29)->startOfDay();

        //$today = $hoje = \Carbon\Carbon::create(2017, 01, 13)->startOfDay();

        if(!$proposal = \App\Proposal::where('id', $request->proposal_id)->where('status', 'SOLD')->first()) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');
        if(!$proposal->payments->count()) return redirect()->back()->with('error', 'Proposta sem pagamentos cadastrados.');

        \Log::info('TRY GENERATE BILLING BEFORE B');

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Proposta não encontrada ou inválida.');
        }

        \Log::info('TRY GENERATE BILLING BEFORE C');

        $periodo = $proposal->correction_type;
        //$periodo = 'Trimestral';

        // printa($periodo);

        if($periodo) {
            switch ($periodo) {
                case 'Anual':           $months = 12;    break;
                case 'Semestral':       $months = 6;     break;
                case 'Trimestral':      $months = 3;     break;
                case 'Bimestral':       $months = 2;     break;
                case 'Mensal':          $months = 1;     break;
                default:                return redirect()->back()->with('error', 'Proposta com período de correção inválido.'); break;
            }
        } else {
            $months = 1;
        }

        \Log::info('TRY GENERATE BILLING BUTTON A');

        //for($i = 0; $i < 36; $i++) {
            // $hoje = $today->copy()->addMonths($i);
            $indice_atual = $proposal->getCurrentIndex($months, $hoje);
            // printa("indice_atual: ".$indice_atual);

            //return;

            if($indice_atual < 0) {
                return redirect()->back()->with('error', 'Valores do Índice de Correção Monetária ausentes.');
                // echo "Valores do Índice de Correção Monetária ausentes."; return;
            }
            //printa($indice_atual);
            // echo "*********************************************** CENÁRIO EM ".$hoje." - VALOR DO ÍNDICE: ".$indice_atual." ***********************************************<br>";
            $proposal->generateBilling($months, $indice_atual, $hoje);
            \Log::info('TRY GENERATE BILLING BUTTON B');
        // }

        // return;

        switch ($proposal->property->block->building->project->send_billets) {
            case 'MES':
                //printa("MES");
                \Log::info('TRY GENERATE BILLING BUTTON PROPOSAL C');
                $proposal->generateBillets($hoje);
            break;
            case 'CICLO':
                //printa("CICLO");
                \Log::info('TRY GENERATE BILLING BUTTON PROPOSAL D');
                $proposal->generateBilletsGroup($months, $hoje);
            break;
            default: \Log::info('OPCAO PARA GERACAO DE BOLETOS INVALIDA'); break;
        }

        //return;

        // \Artisan::call('CreateBillingsCommandTest');
        // return;

        \Log::info('TRY GENERATE BILLING BUTTON E');

        // $proposal->generateBillets($hoje);
        // $proposal->generateBilletsGroup($months, $hoje);
        // $test = new \App\Test();
        // $test->generateBillets($proposal, $months, $hoje);

        /* COMENTEI PQ TAVA DEMORANDO MUITO */
        // \Artisan::call('CheckBilletsCommand');
        // $proposal->generateAmortization();

        // return;
        return redirect()->back();
    }

    public function paid_manual_value(Request $request) {

        if(!$billing = \App\Billing::find($request->id)) return 'Cobrança não encontrada.';

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($billing->payment->proposal->property_id, $properties_ids)) return 'Cobrança não encontrada.';
        }

        $value = $billing->value;

        $h = '  <input type="hidden" name="billing" value="'.$billing->token.'" id="billing_id">
                <div class="row justify-content-center">';
                    if($billing->billet_generated()) {
                        $h .= ' <div class="col-12">
                                    <div class="alert alert-danger rounded-0" role="alert">
                                        <b>Atenção!</b> Verifique se o valor do boleto está correto com os devidos cálculos de desconto, multa e juros.
                                    </div>
                                </div>';
                    }
                    $h .= '<div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label>Valor original da parcela</label>
                            <div class="input-group">
                                <p class="form-control-static m-0">R$ '.formatMoney($billing->value).'</p>
                            </div>
                        </div>
                        <div class="form-group">';
                            if($billing->billet_generated()) {
                                $h .= '<label class="d-flex justify-content-between">Informe o valor do boleto <a href="'.route('billing.billet').'?billet='.$billing->billet_generated()->token.'" target="_BLANK">Boleto atualizado</a></label>';
                            } else {
                                $h .= '<label class="d-flex justify-content-between">Informe o valor da parcela</label>';
                            }
                     $h .= '<div class="input-group">
                                <span class="input-group-append"><span class="input-group-text">R$</span></span>
                                <input type="text" name="billet_value" class="form-control money calculate" required value="0,00" id="billet_value">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Acréscimo</label>
                            <div class="input-group">
                                <p class="form-control-static m-0" id="acrescimo">R$ 0,00</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Informe o valor do desconto</label>
                            <div class="input-group">
                                <span class="input-group-append"><span class="input-group-text">R$</span></span>
                                <input type="text" name="discount_value" class="form-control money calculate" required value="0,00" id="discount_value">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Valor Pago</label>
                            <div class="input-group">
                                <p class="form-control-static m-0" id="pago">R$ 0,00</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="form-group">
                            <label>Observações e detalhes sobre a baixa manual</label>
                            <textarea name="notes" class="form-control" rows="5" required></textarea>
                        </div>
                        <div class="form-group">
                            <label>Informe sua senha para confirmação</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                    </div>
                </div>';

        return $h;
    }

    public function paid_manual_calculate(Request $request) {
        if(!$billing = \App\Billing::where('token', $request->id)->first()) return 'Cobrança não encontrada.';

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($billing->payment->proposal->property_id, $properties_ids)) return 'Cobrança não encontrada.';
        }

        $acrescimo = toCoin($request->billet) - $billing->value;
        $pago = toCoin($request->billet) - toCoin($request->discount);

        return [ 'acrescimo' => 'R$ '.formatMoney($acrescimo), 'pago' => 'R$ '.formatMoney($pago) ];
    }

    public function paid_manual_payment(Request $request) {

        if(!$billing = \App\Billing::where('token', $request->billing)->first()) return redirect()->back()->with('error', 'Cobrança não encontrada.');
        if(!\Hash::check($request->password, \Auth::user()->password)) return redirect()->back()->with('error', 'Senha incorreta.');
        if(in_array($billing->status, [ 'PAID', 'PAID_MANUAL' ])) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($billing->payment->proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Cobrança não encontrada.');
        }

        $paid_value = toCoin($request->billet_value) - toCoin($request->discount_value);
        $extra = toCoin($request->billet_value) - $billing->value;

        // $billing->update([
        //     'value'             => toCoin($request->billet_value),
        //     'discount_value'    => $discount < 0 ? 0 : $discount,
        //     'status'            => 'PAID_MANUAL',
        //     'paid_value'        => toCoin($request->paid_value),
        //     'extra_value'       => $extra < 0 ? 0 : $extra,
        //     'notes'             => $request->notes,
        // ]);

        $billing->update([
            'discount_value'    => toCoin($request->discount_value),
            'status'            => 'PAID_MANUAL',
            'paid_value'        => $paid_value,
            'paid_at'           => \Carbon\Carbon::now(),
            'extra_value'       => $extra < 0 ? 0 : $extra,
            'notes'             => $request->notes,
        ]);

        $billing->setStatus('PAID_MANUAL');

        if($billing->billet_generated()) {
            $plugboleto = new \App\Api\PlugBoleto();
            $retorno = $plugboleto->solicitarBaixa($billing->billet_generated());

            if(isset($retorno->_dados->protocolo) && $retorno->_dados->protocolo) $billing->billet_generated()->update([ 'id_baixa' => $retorno->_dados->protocolo ]);
        }

        return redirect()->back()->with('success', 'Pagamento realizado com sucesso.');
    }

    public function paid_manual_receipt(Request $request) {
        if(!$billing = \App\Billing::where('token', $request->billing)->first()) return redirect()->back()->with('error', 'Cobrança não encontrada.');
        if($billing->status != 'PAID_MANUAL') return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        if (\Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($billing->payment->proposal->property_id, $properties_ids)) return redirect()->back()->with('error', 'Cobrança não encontrada.');
        }

        $name = 'recibo.pdf';
        $pdf = \PDF::loadView('pdf.payment_receipt', [ 'billing' => $billing ]);

        return $pdf->download($name);
    }

    public function ahead_value(Request $request) {

        if(!$payment = \App\Payment::find($request->id)) return 'Pagamento não encontrado.';

        if (!$request->has('client') && \Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($payment->proposal->property_id, $properties_ids)) return 'Pagamento não encontrado.';
        }

        if($request->has('client') && !$payment->proposal->all_proponents->pluck('client_id')->contains(auth()->guard('client')->user()->id)) return 'Pagamento não encontrado.';

        $total_parcelas = $payment->billings->count();
        $parcelas_pagas = $payment->billings->whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->count();
        $parcelas_disponiveis = $total_parcelas - $parcelas_pagas;

        if($ahead = $payment->proposal->pending_ahead()) {
            $billet = $ahead->billet_generated();
            if($billet) {
                $plugboleto = new \App\Api\PlugBoleto();
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

                if(\Carbon\Carbon::now()->startOfDay() > \Carbon\Carbon::parse($billet->emitted_at)->endOfDay()) {
                    return '<div class="text-center">
                                Já existe uma antecipação em aberto para esse pagamento.<br>
                                Realize o pagamento dessa antecipação ou aguarde o vencimento dela para gerar uma nova. (Teste A)
                            </div>';
                } else {
                    return '<div class="text-center">
                            Já existe uma antecipação em aberto para esse pagamento. (Teste B)<br>
                            <a href="'.route('billing.billet').'?billet='.$billet->token.'" target="_BLANK">Visualizar boleto</a>
                        </div>';
                }
            } else {
                $ahead->delete();
            }
        }

        if($payment->pending_ahead()) {
            $billet = $payment->pending_ahead()->billet_generated();
            if($billet) {
                $plugboleto = new \App\Api\PlugBoleto();
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

                // if(\Carbon\Carbon::parse($billet->emitted_at) < \Carbon\Carbon::now()->toDateString()) {
                if(\Carbon\Carbon::now()->startOfDay() > \Carbon\Carbon::parse($billet->emitted_at)->endOfDay()) {
                    return '<div class="text-center">
                                Já existe uma antecipação em aberto para esse pagamento.<br>
                                Realize o pagamento dessa antecipação ou aguarde o vencimento dela para gerar uma nova. (Teste C)
                            </div>';
                } else {
                    return '<div class="text-center">
                            Já existe uma antecipação em aberto para esse pagamento. (Teste D)<br>
                            <a href="'.route('billing.billet').'?billet='.$billet->token.'" target="_BLANK">Visualizar boleto</a>
                        </div>';
                }
            } else {
                $payment->pending_ahead()->delete();
            }
        }

        $options = '';
        if($parcelas_disponiveis) {
            if($parcelas_disponiveis > 12) {
                for ($i = 1; $i <= $parcelas_disponiveis; $i++) {
                    $options .= '<option value="'.$i.'" '.($request->has('qtd') && $request->qtd == $i ? 'selected' : '').'>'.$i.' ('.($i == 1 ? 'última parcela' : 'últimas parcelas').')</option>';
                }
            } else {
                $i = $parcelas_disponiveis;
                $options .= '<option value="'.$i.'" '.($request->has('qtd') && $request->qtd == $i ? 'selected' : '').'>'.$i.' ('.($i == 1 ? 'última parcela' : 'últimas parcelas').')</option>';
            }

            $options = '
                <div class="col-12 col-sm-5">
                    <div class="form-group">
                        <select name="qtd_antecipation" class="form-control" id="qtd_antecipation" required>
                            <option value="0">Selecione...</option>
                            '.$options.'
                        </select>
                    </div>
                </div>
                <div class="col-12 col-sm-3">
                    <button type="button" class="btn btn-info btn-block" id="generate_antecipation" data-payment="'.$payment->id.'">Continuar</button>
                </div>';
        } else {
            $options = '
                <div class="col-12 col-sm-12 text-center">
                    Nenhuma parcela disponível para antecipação.
                </div>';
        }

        $aux = '';
        if($request->has('qtd')) {
            if($request->qtd == 0) {
                $aux = 'A quantidade de parcelas a antecipar deve ser maior que 0.';
            } else {
                $parcelas_antecipadas = $payment->billings->whereNotIn('status', [ 'PAID', 'PAID_MANUAL' ])->sortByDesc('expires_at')->take($request->qtd);

                $aux .= '  <table class="table table-sm table-bordered m-0 text-center">
                                <thead>
                                    <tr>
                                        <th>Token</th>
                                        <th>Data Vencimento</th>
                                        <th>Valor Parcela</th>
                                    </tr>
                                </thead>
                                <tbody>';

                                    foreach ($parcelas_antecipadas as $key => $parcela) {
                                        $aux .= '<tr>
                                                    <td>'.$parcela->token.' <input type="hidden" name="tokens[]" value="'.$parcela->token.'"></td>
                                                    <td>'.formatData($parcela->expires_at).'</td>
                                                    <td>R$ '.formatMoney($parcela->value).'</td>
                                                </tr>';
                                    }

                                    // if($parcelas_disponiveis > 12) {
                                        $antecipacao = $payment->generateAntecipation($request->qtd);
                                    // } else {
                                    //     $antecipacao = $parcelas_antecipadas->sum('value');
                                    // }

                $aux .= '       </tbody>
                                <tfooter>
                                    <tr>
                                        <th rowspan="3"></th>
                                        <th>TOTAL DAS PARCELAS</th>
                                        <th>R$ '.formatMoney($parcelas_antecipadas->sum('value')).'</th>
                                    </tr>
                                    <tr>
                                        <th>DESCONTO TOTAL</th>
                                        <th class="text-danger">- R$ '.formatMoney($parcelas_antecipadas->sum('value') - $antecipacao).'</th>
                                    </tr>
                                    <tr>
                                        <th>TOTAL A SER PAGO</th>
                                        <th>R$ '.formatMoney($antecipacao).'</th>
                                    </tr>
                                </tfooter>
                            </table>';
            }
        }

        $h = '  <input type="hidden" name="payment" value="'.$payment->id.'" id="payment_id">
                <h6 class="text-center">Selecione o número de parcelas que deseja antecipar</h6>
                <div class="row justify-content-center">
                    '.$options.'
                </div>
                <div id="result_antecipation">
                    '.$aux.'
                </div>';

        return $h;
    }

    public function ahead_payment(Request $request) {
        // if(!$request->tokens || !is_array($request->tokens) || !count($request->tokens)) return redirect()->back()->with('error', 'Não foi possível completar a operação.');
        if(!$request->has('payment') || !$request->payment || !$request->has('qtd_antecipation') || !$request->qtd_antecipation) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        $payment = \App\Payment::find($request->payment);

        $total_parcelas = $payment->billings->count();
        $parcelas_pagas = $payment->billings->whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->count();
        $parcelas_disponiveis = $total_parcelas - $parcelas_pagas;

        if($request->qtd_antecipation > $parcelas_disponiveis) return redirect()->back()->with('error', 'Número de parcelas solicitadas maior do que o disponível.');

        if($parcelas_disponiveis <= 12 && $request->qtd_antecipation < $parcelas_disponiveis) return redirect()->back()->with('error', 'Número de parcelas inválido.');

        $parcelas_antecipadas = $payment->billings->whereNotIn('status', [ 'PAID', 'PAID_MANUAL' ])->sortByDesc('expires_at')->take($request->qtd_antecipation);

        // if($parcelas_disponiveis > 12) {
            $antecipacao = $payment->generateAntecipation($request->qtd_antecipation);
        // } else {
        //     $antecipacao = $parcelas_antecipadas->sum('value');
        // }

        $ahead = \App\Ahead::create([
            'payment_id'    => $payment->id,
            'token'         => getBillingToken(8),
            'value'         => $antecipacao,
            'status'        => 'PENDING',
            'billings'      => json_encode($parcelas_antecipadas->pluck('id')->toArray())
        ]);

        $ahead->setStatus('PENDING');

        if(!$ahead->billet_generated()) {
            $billet = \App\Billet::create([
                'billetable_type'   => 'App\Ahead',
                'billetable_id'     => $ahead->id,
                'token'             => getBillingToken(8)
            ]);

            $plugboleto = new \App\Api\PlugBoleto();
            $boleto = $plugboleto->postAheadBoleto($ahead, $billet);

            // printa($boleto);

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

        // return;

        return redirect()->back();
    }

    public function ahead_total_value(Request $request) {
        if(!$request->has('proposal') || !$request->proposal) return [ 'error' => 'Não foi possível completar a operação.' ];
        if(!$proposal = \App\Proposal::find($request->proposal)) return [ 'error' => 'Contrato não encontrado.' ];

        if (!$request->has('client') && \Auth::user()->role == 'INCORPORATOR') {
            $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get();

            $properties_ids = array();
            foreach ($projects as $key => $project) {
                if(!\Auth::user()->checkPermission($project->id, ['FINANCIAL_MODULE_ACCESS'])) continue;
                $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
            }
            $properties_ids = array_unique($properties_ids);

            if(!in_array($proposal->property_id, $properties_ids)) return [ 'error' => 'Contrato não encontrado.' ];
        }

        if($request->has('client') && !$proposal->all_proponents->pluck('client_id')->contains(auth()->guard('client')->user()->id)) return [ 'error' => 'Contrato não encontrado.' ];

        if($ahead = $proposal->pending_ahead()) {
            $billet = $ahead->billet_generated();

            $plugboleto = new \App\Api\PlugBoleto();
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

            if(\Carbon\Carbon::now()->startOfDay() > \Carbon\Carbon::parse($billet->emitted_at)->endOfDay()) {
                return [ 'error' => '<div class="text-center">
                                        Já existe uma antecipação em aberto para esse pagamento.<br>
                                        Realize o pagamento dessa antecipação ou aguarde o vencimento dela para gerar uma nova.
                                    </div>' ];
            } else {
                return [ 'error' => '<div class="text-center">
                                        Já existe uma antecipação em aberto para esse pagamento.<br>
                                        <a href="'.route('billing.billet').'?billet='.$billet->token.'" target="_BLANK">Visualizar boleto</a>
                                    </div>' ];
            }
        }

        foreach ($proposal->payments as $key => $payment) {
            if($payment->pending_ahead()) {
                $billet = $payment->pending_ahead()->billet_generated();

                $plugboleto = new \App\Api\PlugBoleto();
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

                return [ 'error' => '<div class="text-center">
                                        Já existe uma antecipação em aberto para esse contrato.<br>
                                        Realize o pagamento dessa antecipação ou aguarde o vencimento dela para gerar uma nova.
                                    </div>' ];
            }
        }

        $aux = '';
        $total_todas_parcelas = $desconto_total_todas_parcelas = $total_pago_todas_parcelas = 0;
        foreach ($proposal->payments as $key => $payment) {
            $total_parcelas = $payment->billings->count();
            $parcelas_pagas = $payment->billings->whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->count();
            $parcelas_disponiveis = $total_parcelas - $parcelas_pagas;
            if(!$parcelas_disponiveis) continue;

            $parcelas_antecipadas = $payment->billings->whereNotIn('status', [ 'PAID', 'PAID_MANUAL' ])->sortByDesc('expires_at');

            $aux .= '   <label>Pagamento - Componenete: '.$payment->component.' | Método: '.$payment->method.'</label>
                        <table class="table table-sm table-bordered text-center">
                            <thead>
                                <tr>
                                    <th>Token</th>
                                    <th>Data Vencimento</th>
                                    <th>Valor Parcela</th>
                                </tr>
                            </thead>
                            <tbody>';
                                foreach ($parcelas_antecipadas as $key => $parcela) {
                                    $aux .= '<tr>
                                                <td>'.$parcela->token.' <input type="hidden" name="tokens[]" value="'.$parcela->token.'"></td>
                                                <td>'.formatData($parcela->expires_at).'</td>
                                                <td>R$ '.formatMoney($parcela->value).'</td>
                                            </tr>';
                                }
                                $antecipacao = $payment->generateAntecipation($parcelas_antecipadas->count());

                                $total_todas_parcelas += $parcelas_antecipadas->sum('value');
                                $desconto_total_todas_parcelas += ($parcelas_antecipadas->sum('value') - $antecipacao);
                                $total_pago_todas_parcelas += $antecipacao;
            $aux .= '       </tbody>
                            <tfooter>
                                <tr>
                                    <th rowspan="3"></th>
                                    <th>TOTAL DAS PARCELAS</th>
                                    <th>R$ '.formatMoney($parcelas_antecipadas->sum('value')).'</th>
                                </tr>
                                <tr>
                                    <th>DESCONTO TOTAL</th>
                                    <th class="text-danger">- R$ '.formatMoney($parcelas_antecipadas->sum('value') - $antecipacao).'</th>
                                </tr>
                                <tr>
                                    <th>TOTAL A SER PAGO</th>
                                    <th>R$ '.formatMoney($antecipacao).'</th>
                                </tr>
                            </tfooter>
                        </table>';
        }

        $aux .= '   <label>Total de todos os pagamentos</label>
                    <table class="table table-sm table-bordered text-center">
                        <tfooter>
                            <tr>
                                <th rowspan="3"></th>
                                <th>TOTAL DAS PARCELAS</th>
                                <th>R$ '.formatMoney($total_todas_parcelas).'</th>
                            </tr>
                            <tr>
                                <th>DESCONTO TOTAL</th>
                                <th class="text-danger">- R$ '.formatMoney($desconto_total_todas_parcelas).'</th>
                            </tr>
                            <tr>
                                <th>TOTAL A SER PAGO</th>
                                <th>R$ '.formatMoney($total_pago_todas_parcelas).'</th>
                            </tr>
                        </tfooter>
                    </table>';

        return [ 'success' => $aux ];
    }

    public function ahead_total_generate(Request $request) {
        if(!$request->has('proposal') || !$request->proposal) return redirect()->back()->with('error', 'Não foi possível completar a operação.');
        if(!$proposal = \App\Proposal::find($request->proposal)) return redirect()->back()->with('error', 'Contrato não encontrado.');

        if($proposal->pending_ahead()) return redirect()->back()->with('error', 'Já existe uma antecipação em aberto nesse contrato.');

        foreach ($proposal->payments as $key => $payment) {
            if($payment->pending_ahead()) {
                return redirect()->back()->with('error', 'Já existe uma antecipação em aberto nesse contrato.');
            }
        }

        $total_pago_todas_parcelas = $payment_id = 0;
        $parcelas_antecipadas_ids = [];
        foreach ($proposal->payments as $key => $payment) {
            $total_parcelas = $payment->billings->count();
            $parcelas_pagas = $payment->billings->whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->count();
            $parcelas_disponiveis = $total_parcelas - $parcelas_pagas;
            if(!$parcelas_disponiveis) continue;

            $parcelas_antecipadas = $payment->billings->whereNotIn('status', [ 'PAID', 'PAID_MANUAL' ])->sortByDesc('expires_at');
            $parcelas_antecipadas_ids = array_merge($parcelas_antecipadas_ids, $parcelas_antecipadas->pluck('id')->toArray());

            $antecipacao = $payment->generateAntecipation($parcelas_antecipadas->count());
            $total_pago_todas_parcelas += $antecipacao;
            $payment_id = $payment->id;
        }

        if(!$total_pago_todas_parcelas) return redirect()->back()->with('error', 'Valor do pagamento inválido.');

        $ahead = \App\Ahead::create([
            'payment_id'    => $payment_id,
            'token'         => getBillingToken(8),
            'value'         => $total_pago_todas_parcelas,
            'status'        => 'PENDING',
            'billings'      => json_encode($parcelas_antecipadas_ids),
            'is_total'      => 1
        ]);

        $ahead->setStatus('PENDING');

        if(!$ahead->billet_generated()) {
            $billet = \App\Billet::create([
                'billetable_type'   => 'App\Ahead',
                'billetable_id'     => $ahead->id,
                'token'             => getBillingToken(8)
            ]);

            $plugboleto = new \App\Api\PlugBoleto();
            $boleto = $plugboleto->postAheadBoleto($ahead, $billet);

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

        return redirect()->back();
    }

    public function billet_test_generate(Request $request) {

        if(!$request->has('account') || !$request->account) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        if(!$account = \App\Account::find($request->account)) return redirect()->back()->with('error', 'Conta não encontrada');

        $test = \App\BillingTest::create([
            'account_id'                => $account->id,
            'value'                     => toCoin($request->value),
            'quantity'                  => $request->quantity,
            'TituloDocEspecie'          => $request->TituloDocEspecie,
            'TituloAceite'              => $request->TituloAceite,
            'TituloLocalPagamento'      => $request->TituloLocalPagamento,
            'TituloCodEmissaoBloqueto'  => $request->TituloCodEmissaoBloqueto
        ]);

        $plugboleto = new \App\Api\PlugBoleto();
        $retorno = $plugboleto->postBoletoTest($account, $test);

        if(isset($retorno->_dados->_sucesso) && is_array($retorno->_dados->_sucesso) && count($retorno->_dados->_sucesso)) {
            foreach($retorno->_dados->_sucesso as $sucesso) {
                $id = $sucesso->TituloNossoNumero - $account->inicio_nosso_numero;
                if(!$billet = \App\Billet::where('id', $id)->where('billetable_type', 'App\BillingTest')->first()) continue;

                if(isset($sucesso->situacao)) {
                    $billet->update([ 'status' => $sucesso->situacao, 'idIntegracao' => $sucesso->idintegracao ]);
                    $billet->setStatus($sucesso->situacao);
                }
            }
        }

        if(isset($retorno->_dados->_falha[0]->_erros)) {
            $billet = \App\Billet::where('billetable_type', 'App\BillingTest')->latest()->first;

            \App\BilletError::create([
                'billet_id' => $billet->id,
                'message'   => json_encode($retorno->_dados->_falha[0]->_erros)
            ]);
        }

        return redirect()->back();
    }

    public function billet_test_view(Request $request) {
        if(!$account = \App\Account::find($request->id)) return 'Conta não encontrada.';

        $plugboleto = new \App\Api\PlugBoleto();

        $tests = \App\BillingTest::where('account_id', $account->id)->latest()->get();

        if(!$tests->count()) return 'Nenhuma cobrança de teste encontrada para essa conta.';

        $plugboleto = new \App\Api\PlugBoleto();

        $aux = '';
        foreach ($tests as $key => $test) {
            $aux2 = '';

            $send = $test->billets->where('idIntegracao', '!=', null)->where('status', '!=', 'FALHA');
            if($send->count()) {
                $boletos = $plugboleto->getBoletosColecao($send);

                \Log::info(serialize($boletos));

                if(isset($boletos->_dados) && count($boletos->_dados)) {
                    foreach ($boletos->_dados as $key => $dado) {
                        if(!isset($dado->IdIntegracao)) continue;
                        if(!$search = \App\Billet::where('idIntegracao', $dado->IdIntegracao)->first()) continue;

                        $search->update([
                            'status'                => isset($dado->situacao)               ? $dado->situacao               : null,
                            'bar_code'              => isset($dado->TituloLinhaDigitavel)   ? $dado->TituloLinhaDigitavel   : null,
                            'PagamentoData'         => isset($dado->PagamentoData)          ? $dado->PagamentoData          : null,
                            'PagamentoRealizado'    => isset($dado->PagamentoRealizado)     ? $dado->PagamentoRealizado     : null,
                            'PagamentoValorPago'    => isset($dado->PagamentoValorPago)     ? $dado->PagamentoValorPago     : null,
                        ]);

                        if(isset($dado->motivo) && $dado->motivo) {
                            \App\BilletError::create([
                                'billet_id' => $search->id,
                                'message'   => $dado->motivo
                            ]);
                        }
                    }
                }
            }

            $billets = \App\Billet::where('billetable_type', 'App\BillingTest')->where('billetable_id', $test->id)->get();

            foreach ($test->billets as $billet) {
                // if($billet->status != 'FALHA' && $billet->idIntegracao) {
                //     $boleto = $plugboleto->getBoletos($billet);

                //     $billet->update([
                //         'status'                => isset($boleto->_dados[0]->situacao) ? $boleto->_dados[0]->situacao : null,
                //         'bar_code'              => isset($boleto->_dados[0]->TituloLinhaDigitavel) ? $boleto->_dados[0]->TituloLinhaDigitavel : null,
                //         'PagamentoData'         => isset($boleto->_dados[0]->PagamentoData) ? $boleto->_dados[0]->PagamentoData : null,
                //         'PagamentoRealizado'    => isset($boleto->_dados[0]->PagamentoRealizado) ? $boleto->_dados[0]->PagamentoRealizado : null,
                //         'PagamentoValorPago'    => isset($boleto->_dados[0]->PagamentoValorPago) ? $boleto->_dados[0]->PagamentoValorPago : null,
                //     ]);

                //     if(isset($boleto->_dados[0]->motivo) && $boleto->_dados[0]->motivo) {
                //         \App\BilletError::create([
                //             'billet_id' => $billet->id,
                //             'message'   => $boleto->_dados[0]->motivo
                //         ]);
                //     }
                // }

                $aux2 .= '  <tr>
                                <td>'.($billet->status != 'FALHA' ? '<input type="checkbox" name="billets[]" value="'.$billet->id.'">' : '').'</td>
                                <td>'.$billet->token.'</td>
                                <td>'.$billet->status.'</td>
                                <td>';
                                    if($billet->errors->count()) {
                                        foreach ($billet->errors as $error) {
                                            $aux2 .= '<div>'.$error->message.'</div>';
                                        }
                                    }
                      $aux2 .= '</td>
                                <td>'.($billet->status != 'FALHA' ?
                                    '<div class="dropdown dropleft">
                                        <button class="btn btn-secondary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Ações</button>
                                        <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                            <a class="dropdown-item" href="'.route('billing.billet').'?billet='.$billet->token.'" target="_BLANK">Visualizar boleto</a>
                                        </div>
                                    </div>' : '').'
                                </td>
                            </tr>';
            }

            $aux .= '   <div class="card">
                            <div class="card-header" data-toggle="collapse" data-target="#collapse'.$key.'">Valor: R$'.formatMoney($test->value).' -- Quantidade: '.$test->quantity.' -- Criado em: '.dateTimeStringBR($test->created_at).'</div>
                            <div id="collapse'.$key.'" class="collapse" data-parent="#accordion">
                                <div class="card-body">
                                    <table class="table table-sm table-bordered text-center mb-5">
                                        <thead>
                                            <tr>
                                                <th width="10%"></th>
                                                <th width="20%">Token</th>
                                                <th width="15%">Status</th>
                                                <th>Erros</th>
                                                <th width="10%">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            '.$aux2.'
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>';
        }

        return '<div class="accordion" id="accordion">
                    '.$aux.'
                </div>';
    }

    public function billet_test(Request $request) {

        if(!$account = \App\Account::find($request->id)) return 'Conta não encontrada.';

        $plugboleto = new \App\Api\PlugBoleto();

        $cedente = $plugboleto->getCedente($account->owner->document);
        if(isset($cedente->_dados[0]->id)) {
            if(isset($cedente->_dados[0]->id)) $account->owner->update([ 'plugboleto_id' => $cedente->_dados[0]->id ]);

            $aux_cedente = '    <div class="d-flex justify-content-between"><b>Id</b><span>'.$cedente->_dados[0]->id.'</span></div>
                                <div class="d-flex justify-content-between"><b>Razão Social</b><span>'.$cedente->_dados[0]->razaosocial.'</span></div>
                                <div class="d-flex justify-content-between"><b>Nome Fantasia</b><span>'.$cedente->_dados[0]->nomefantasia.'</span></div>
                                <div class="d-flex justify-content-between"><b>CPF/CNPJ</b><span>'.$cedente->_dados[0]->cpf_cnpj.'</span></div>
                                <div class="d-flex justify-content-between"><b>Logradouro</b><span>'.$cedente->_dados[0]->logradouro.'</span></div>
                                <div class="d-flex justify-content-between"><b>Número</b><span>'.$cedente->_dados[0]->numero.'</span></div>
                                <div class="d-flex justify-content-between"><b>Complemento</b><span>'.$cedente->_dados[0]->complemento.'</span></div>
                                <div class="d-flex justify-content-between"><b>Bairro</b><span>'.$cedente->_dados[0]->bairro.'</span></div>
                                <div class="d-flex justify-content-between"><b>CEP</b><span>'.$cedente->_dados[0]->cep.'</span></div>
                                <div class="d-flex justify-content-between"><b>UF</b><span>'.$cedente->_dados[0]->uf.'</span></div>
                                <div class="d-flex justify-content-between"><b>Telefone</b><span>'.$cedente->_dados[0]->telefone.'</span></div>
                                <div class="d-flex justify-content-between"><b>E-Mail</b><span>'.$cedente->_dados[0]->email.'</span></div>';
        } else {
            /* CEDENTE NÂO ENCONTRADO */
            $aux_cedente = '    <div>Cedente ainda não registrado.</div>
                                <div>A emissão de boletos não será possível.</div>';
        }

        \Log::info(serialize($account));
        $conta = $plugboleto->getConta($account);
        \Log::info(serialize($conta));
        if(isset($conta->_dados[0]->id)) {
            if(isset($conta->_dados[0]->id)) $account->update([ 'plugboleto_id' => $conta->_dados[0]->id ]);

            $aux_conta = '  <div class="d-flex justify-content-between"><b>Id</b><span>'.$conta->_dados[0]->id.'</span></div>
                            <div class="d-flex justify-content-between"><b>Código Banco</b><span>'.$conta->_dados[0]->codigo_banco.'</span></div>
                            <div class="d-flex justify-content-between"><b>Agência</b><span>'.$conta->_dados[0]->agencia.'</span></div>
                            <div class="d-flex justify-content-between"><b>Agência DV</b><span>'.$conta->_dados[0]->agencia_dv.'</span></div>
                            <div class="d-flex justify-content-between"><b>Conta</b><span>'.$conta->_dados[0]->conta.'</span></div>
                            <div class="d-flex justify-content-between"><b>Conta DV</b><span>'.$conta->_dados[0]->conta_dv.'</span></div>
                            <div class="d-flex justify-content-between"><b>Tipo da Conta</b><span>'.$conta->_dados[0]->tipo_conta.'</span></div>
                            <div class="d-flex justify-content-between"><b>Código do Beneficiário</b><span>'.$conta->_dados[0]->cod_beneficiario.'</span></div>
                            <div class="d-flex justify-content-between"><b>Código da Empresa</b><span>'.$conta->_dados[0]->cod_empresa.'</span></div>
                            <div class="d-flex justify-content-between"><b>Validação Ativa</b><span>'.($conta->_dados[0]->validacao_ativa ? 'Sim' : 'Não').'</span></div>
                            <div class="d-flex justify-content-between"><b>Impressão Atualizada</b><span>'.($conta->_dados[0]->impressao_atualizada ? 'Sim' : 'Não').'</span></div>';
        } else {
            /* CONTA NÃO ENCONTRADA */
            $aux_conta = '  <div>Conta ainda não registrada.</div>
                            <div>A emissão de boletos não será possível.</div>';
        }

        $convenio = $plugboleto->getConvenio($account->agreement);
        if(isset($convenio->_dados[0]->id)) {
            if(isset($convenio->_dados[0]->id)) $account->agreement->update([ 'plugboleto_id' => $convenio->_dados[0]->id ]);

            $aux_convenio = '  <div class="d-flex justify-content-between"><b>Id</b><span>'.$convenio->_dados[0]->id.'</span></div>
                                <div class="d-flex justify-content-between"><b>Número</b><span>'.$convenio->_dados[0]->numero_convenio.'</span></div>
                                <div class="d-flex justify-content-between"><b>Descrição</b><span>'.$convenio->_dados[0]->descricao_convenio.'</span></div>
                                <div class="d-flex justify-content-between"><b>Carteira</b><span>'.$convenio->_dados[0]->carteira.'</span></div>
                                <div class="d-flex justify-content-between"><b>Espécie</b><span>'.$convenio->_dados[0]->especie.'</span></div>
                                <div class="d-flex justify-content-between"><b>CNAB</b><span>'.$convenio->_dados[0]->padraoCNAB.'</span></div>
                                <div class="d-flex justify-content-between"><b>Transmissão Automática</b><span>'.($convenio->_dados[0]->utiliza_van ? 'Sim' : 'Não').'</span></div>
                                <div class="d-flex justify-content-between"><b>Número Remessa</b><span>'.$convenio->_dados[0]->numero_remessa.'</span></div>
                                <div class="d-flex justify-content-between"><b>Densidade Remessa</b><span>'.$convenio->_dados[0]->densidade_remessa.'</span></div>
                                <div class="d-flex justify-content-between"><b>Código Cobrança</b><span>'.$convenio->_dados[0]->codigo_cobranca.'</span></div>
                                <div class="d-flex justify-content-between"><b>Reiniciar Diariamente</b><span>'.($convenio->_dados[0]->reiniciar_diariamente ? 'Sim' : 'Não').'</span></div>';
        } else {
            /* CONVÊNIO NÃO ENCONTRADO */
            $aux_convenio = '   <div>Convênio ainda não registrado.</div>
                                <div>A emissão de boletos não será possível.</div>';
        }

        $h = '  <div class="row">
                    <div class="col-12 col-sm-6">
                        <div class="accordion" id="accordion">
                            <div class="card">
                                <div class="card-header" data-toggle="collapse" data-target="#collapseCedente">Dados Cedente</div>
                                <div id="collapseCedente" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                    '.$aux_cedente.'
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" data-toggle="collapse" data-target="#collapseConta">Dados Conta</div>
                                <div id="collapseConta" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                    '.$aux_conta.'
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header" data-toggle="collapse" data-target="#collapseConvenio">Dados Convênio</div>
                                <div id="collapseConvenio" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                    '.$aux_convenio.'
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <input type="hidden" name="account" value="'.$account->id.'">
                        <div class="form-group">
                            <label>Valor do boleto</label>
                            <div class="input-group">
                                <span class="input-group-prepend"><span class="input-group-text">R$</span></span>
                                <input type="text" name="value" class="form-control money" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Quantidade</label>
                                    <input type="number" name="quantity" class="form-control" min="1" value="1" required>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Espécie do Documento</label>
                                    <select name="TituloDocEspecie" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <option value="01">01 - Duplicata Mercantil</option>
                                        <option value="02">02 - Nota promissória</option>
                                        <option value="03">03 - Nota de seguro</option>
                                        <option value="04">04 - Duplicata de Serviço</option>
                                        <option value="05">05 - Recibo</option>
                                        <option value="06">06 - Letra de Câmbio</option>
                                        <option value="07">07 - Nota de Débito</option>
                                        <option value="08">08 - Boleto de Proposta</option>
                                        <option value="09">09 - Letra de Câmbio</option>
                                        <option value="10">10 - Warrant</option>
                                        <option value="11">11 - Cheque</option>
                                        <option value="12">12 - Cobrança Seriada</option>
                                        <option value="13">13 - Mensalidade escolar</option>
                                        <option value="14">14 - Apólice de Seguro</option>
                                        <option value="15">15 - Documento de Dívida</option>
                                        <option value="16">16 - Encargos Condominiais</option>
                                        <option value="17">17 - Conta de prestação de serviço</option>
                                        <option value="18">18 - Contrato</option>
                                        <option value="19">19 - Cosseguro</option>
                                        <option value="20">20 - Duplicata Rural</option>
                                        <option value="21">21 - Nota Promissória Rural</option>
                                        <option value="22">22 - Dívida Ativa da União</option>
                                        <option value="23">23 - Dívida Ativa de Estado</option>
                                        <option value="24">24 - Dívida Ativa de Município</option>
                                        <option value="25">25 - Duplicata Mercantil por Indicação</option>
                                        <option value="26">26 - Duplicata de Serviço por Indicação</option>
                                        <option value="27">27 - Nota de Crédito Comercial</option>
                                        <option value="28">28 - Nota de Crédito para Exportação</option>
                                        <option value="29">29 - Nota de Crédito Industrial</option>
                                        <option value="30">30 - Nota de Crédito Rural</option>
                                        <option value="32">32 - Triplicata Mercantil</option>
                                        <option value="33">33 - Triplicata de Serviço</option>
                                        <option value="34">34 - Fatura</option>
                                        <option value="35">35 - Parcela de Consórcio</option>
                                        <option value="36">36 - Nota Fiscal</option>
                                        <option value="37">37 - Cédula de Produto Rural</option>
                                        <option value="38">38 - Cartão de crédito</option>
                                        <option value="99">99 - Outros</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Código Emissão do Bloqueto</label>
                                    <select name="TituloCodEmissaoBloqueto" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <option value="0">0 - Não aceita</option>
                                        <option value="1">1 - Banco Emite</option>
                                        <option value="2">2 - Cliente Emite</option>
                                        <option value="3">3 - Banco Pré-emite e Cliente Complementa</option>
                                        <option value="4">4 - Banco Reemite</option>
                                        <option value="5">5 - Banco Não Reemite</option>
                                        <option value="7">7 - Banco Emitente - Aberta</option>
                                        <option value="8">8 - Banco Emitente - Auto-envelopável</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    <label>Aceite</label>
                                    <select name="TituloAceite" class="form-control" required>
                                        <option value="">Selecione...</option>
                                        <option value="S">S</option>
                                        <option value="N">N</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Local de Pagamento</label>
                            <input type="text" class="form-control" name="TituloLocalPagamento" maxlength="200" required>
                        </div>
                    </div>
                </div>';

        return $h;
    }
}
