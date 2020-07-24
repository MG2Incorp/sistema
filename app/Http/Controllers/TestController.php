<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Constructor;
use App\User;
use App\Mailer;
use App\CompanyProject;
use App\UserProject;
use App\Proposal;
use App\Project;

use Auth;
use Exception;
use Hash;
use PDF;

class TestController extends Controller
{
    private $data = array();

    public function index(Request $request) {
        return;
        
        $building = \App\Building::find(17);

        foreach($building->blocks->sortBy('label') as $key => $block) {
            printa($block->properties->sortBy('number', SORT_NUMERIC, false)->pluck('number'));           
        }

        return;

        $payment = \App\Payment::find(572);

        $antecipation = $payment->generateAntecipation(3);

        printa($antecipation);
        return;

        // \Artisan::call('CheckProposalsCommand');
        // \Artisan::call('CheckAntecipationsCommand');
        \Artisan::call('CheckBillingsCommand');

        return;

        $proposals = \App\Proposal::where('correction_index', 1)->where('status', 'SOLD')->get()->reject(function ($value, $key) {
            return in_array($value->property->block->building->project_id, [2, 3]);
        });

        //printa($proposals->count());

        // foreach ($proposals as $key => $proposal) {
        //     echo $proposal->id."<br>";
        // }

        $ids = $proposals->pluck('id')->toArray();


        $payments = \App\Payment::whereIn('proposal_id', $ids)->get();
        //printa($payments->count());

        $aux = [];
        foreach ($payments as $key => $payment) {
            $aux[$payment->id] = $payment->proposal_id;
        }

        $ids = $payments->pluck('id');

        $histories = \App\PaymentInstallmentHistory::whereIn('payment_id', $ids)->get()->groupBy('payment_id')->reject(function ($value, $key) {
            return count($value) == 1;
        });

        //printa($histories->count());

        $billings = \App\Billing::whereIn('status', [ 'PAID', 'PAID_MANUAL' ])->where('paid_value', '!=', 0)->whereColumn('value', '!=', 'paid_value')->get();
        printa($billings->count());
        // return;

        $list = [199,232,250,287,286,284,310,309,399,400,401,402,406,407,408,409,410,411,412,413,414,415,417,418,419,420,403,421,398,397,396,395,394,393,392,390,389,383,380,372,375,376,377,378,379,332,
                 318,319,320,321,322,324,325,326,328,329,330,331,334,335,336,337,338,351,352,354,355,356,357,358,360,361,363,364,365,366,367,317,339,340,341,342,343,344,345,277,275,278,279,345,274,273,
                 272,271,276,280,281,282,285,289,292,293,295,296,297,299,300,301,302,306,307,308,309,298];

        printa(count($list));

        $count = 0;

        foreach($histories as $key => $history) {
            $array = [];
            foreach ($history as $h) {
                // if(!in_array($h->value, $array))
                $array[] = $h->value;
            }
            if(count($array) > 1) {
                // printa($array);
                // $corrige = false;
                // foreach ($array as $i => $arr) {
                //     $next = $i + 1;
                //     if(isset($array[$next])) {

                //         if($array[$next] < $arr) {
                //             $corrige = true;
                //             break;
                //         }
                //     }
                // }

                // if(!$corrige) continue;
                if($aux[$key] > 421) continue;

                if(!in_array($aux[$key], $list)) continue;


                $filter = $billings->where('payment_id', $key)->where('year', 2020);
                if($filter->count()) {
                    echo "PAGAMENTO: ".$key." -- PROPOSTA: ".$aux[$key]."<br>";
                    foreach($filter as $f) {
                        $has = $history->where('month', $f->month)->where('year', $f->year)->first();
                        if($has) {
                            echo $f->month."/".$f->year." -- VALUE: ".$f->value." -- PAID VALUE: ".$f->paid_value." -- REAL VALUE: ".$has->value."<br>";
                        } else {
                            echo $f->month."/".$f->year." -- VALUE: ".$f->value." -- PAID VALUE: ".$f->paid_value." -- REAL VALUE: <br>";
                        }
                    }
                    $count++;
                    echo "<br><br>";
                }

                // foreach ($history as $h) {
                //     echo $h->month."/".$h->year." --- ".$h->value."<br>";
                // }
            }
        }

        printa($count);

        return;

        $api = new \App\Api\PlugBoleto();
        $billet = $api->getBoletos(\App\Billet::find(314));
        printa($billet);
        return;

        // \Artisan::call('CreateBillingsCommand');
        return;

        $this->data['breadcrumb'][] = ['text' => 'Propostas', 'is_link' => 0, 'link' => null];

        $filters = $params = array();
        if ($request->has('start'))     $filters['start'] = $request->start;
        if ($request->has('end'))       $filters['end'] = $request->end;
        if ($request->has('number'))    $filters['number'] = $request->number;
        if ($request->has('status'))    $filters['status'] = $request->status;
        if ($request->has('proponent')) $filters['proponent'] = $request->proponent;
        if ($request->has('project'))   $filters['project'] = $request->project;

        $filters = array_filter($filters, 'strlen');
        $this->data['filters'] = $filters;

        $builder = '';
        $status = null;
        if(count($filters)) $builder = '&'.http_build_query($filters);
        $this->data['builder'] = $builder;

        switch (\Auth::user()->role) {
            case 'ADMIN': $projects = \App\Project::all(); break;
            case 'INCORPORATOR': $projects = \App\Project::where('constructor_id', \Auth::user()->constructor_id)->get(); break;
            case 'COORDINATOR':
                $companies = \Auth::user()->user_companies->where('is_coordinator', 1)->pluck('company_id')->toArray();
                $ids = \App\UserProject::where('user_id', \Auth::user()->id)->whereIn('company_id', $companies)->get()->pluck('project_id')->toArray();
                $projects = \App\Project::whereIn('id', $ids)->get();
            break;
            case 'AGENT':
                $ids = \App\UserProject::where('user_id', \Auth::user()->id)->get()->pluck('project_id')->toArray();
                $projects = \App\Project::whereIn('id', $ids)->get();
            break;
            default: return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
        }

        $property_ids = array();
        foreach ($projects as $key => $project) {
            $property_ids = array_merge($property_ids, $project->properties->pluck('id')->toArray());
        }
        $property_ids = array_unique($property_ids);

        $this->data['projects'] = $projects;

        $propostas = Proposal::latest();

        $has_proponent = false;
        foreach ($filters as $key => $filter) {
            switch ($key) {
                case 'project':
                    $params['project'] = 'Todos';
                    if($filter != 'ALL') {
                        $project = \App\Project::find($filter);
                        if(!\Auth::user()->projects->contains('id', $filter)) return redirect()->back()->with('error', 'Você não tem permissão para completar essa operação.');
                        $properties_ids = $project->properties->pluck('id')->toArray();
                        $params['project'] = $project->name;
                    }
                break;
                case 'status':
                    $params['status'] = 'Todos';
                    if($filter != 'ALL') {
                        $propostas = $propostas->where('status', $filter);
                        $params['status'] = $filter;
                    }
                break;
                case 'number':
                    $params['number'] = $filter;
                    $propostas = $propostas->where('id', 'LIKE', '%'.$filter.'%');
                break;
                case 'proponent':
                    $params['proponent'] = $filter;
                    $has_proponent = true;
                    $prop = $filter;
                break;
                case 'start': case 'end':
                    $has_period = true;
                    $start = $filters['start'];
                    $end = $filters['end'];

                    $propostas = $propostas->whereBetween('created_at', [ \Carbon\Carbon::parse($filters['start']), \Carbon\Carbon::parse($filters['end']) ]);

                    $params['start'] = formatData($start);
                    $params['end'] = formatData($end);
                break;
            }
        }

        switch (Auth::user()->role) {
            case 'ADMIN':
                if(isset($properties_ids)) {
                    $founds = $propostas->whereIn('property_id', $properties_ids)->paginate(20);
                } else {
                    $founds = $propostas->paginate(20);
                }
            break;
            case 'INCORPORATOR':
                $projects = Project::where('constructor_id', Auth::user()->constructor_id)->get();

                if(!isset($properties_ids)) {
                    $properties_ids = array();
                    foreach ($projects as $key => $project) {
                        $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
                    }
                    $properties_ids = array_unique($properties_ids);
                }

                $founds = $propostas->whereIn('property_id', $properties_ids)->paginate(20);
            break;
            case 'COORDINATOR':
                $companies = \Auth::user()->user_companies->where('is_coordinator', 1)->pluck('company_id')->toArray();

                $colegas = \App\UserCompany::whereIn('company_id', $companies)->get()->pluck('user_id')->toArray();

                $ids = \App\UserProject::where('user_id', \Auth::user()->id)->whereIn('company_id', $companies)->get()->pluck('project_id')->toArray();
                $projects = \App\Project::whereIn('id', $ids)->get();

                if(!isset($properties_ids)) {
                    $properties_ids = [];
                    foreach ($projects as $key => $project) {
                        $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
                    }
                }

                $founds = $propostas->whereIn('property_id', $properties_ids)->whereIn('user_id', $colegas)->paginate(20);
            break;
            case 'AGENT':
                $founds = $propostas->where('user_id', Auth::user()->id)->paginate(20);
            break;
            default:
                $founds = array();
            break;
        }

        if($has_proponent) {
            $founds = $founds->reject(function($value, $key) use ($prop) { return strripos($value->main_proponent->name, $prop) === false; });
        }

        $this->data['proposals'] = $founds;

        return view('tests', $this->data);

        return;


        // if(in_array((string) $hoje->month."-".$hoje->year, $meses_com_reajustes)) {
        //     \Log::info('TRY GENERATE BILLING BUTTON BILLETS GROUP DEPOIS CHECK MES REAJUSTE');

        //     $pos = array_search((string) $hoje->month."-".$hoje->year, $meses_com_reajustes);
        //     /* PEGA O PRÓXIMO MÊS COM REAJUSTE QUE VAI SER O LIMITE PARA ENVIAR OS BOLETOS */
        //     $end = $pos + 1;

        //     $prazo_inicial = \Carbon\Carbon::createFromDate($hoje->year, $hoje->month, null)->startOfMonth();

        //     if(isset($meses_com_reajustes[$end])) {
        //         $explode = explode('-', $meses_com_reajustes[$end]);
        //         $prazo_final = \Carbon\Carbon::createFromDate($explode[1], $explode[0], null)->subMonth()->endOfMonth();
        //         $billings = \App\Billing::where('payment_id', $payment->id)->whereBetween('expires_at', [ $prazo_inicial, $prazo_final ])->get();
        //     } else {
        //         $billings = \App\Billing::where('payment_id', $payment->id)->where('expires_at', '>', $prazo_inicial)->get();
        //     }
        // } else {
        //     $prazo_inicial = \Carbon\Carbon::createFromDate($hoje->year, $hoje->month, null)->startOfMonth();

        //     $proximo_reajuste = null;
        //     for ($i = 1; $i < 13; $i++) {
        //         $aux = $prazo_inicial->copy();
        //         $parcela = $aux->addMonths($i);

        //         if(in_array((string) $parcela->month."-".$parcela->year, $meses_com_reajustes)) {
        //             $proximo_reajuste = array_search((string) $parcela->month."-".$parcela->year, $meses_com_reajustes);
        //             break;
        //         }
        //     }

        //     if($proximo_reajuste && isset($meses_com_reajustes[$proximo_reajuste])) {
        //         $explode = explode('-', $meses_com_reajustes[$proximo_reajuste]);
        //         $prazo_final = \Carbon\Carbon::createFromDate($explode[1], $explode[0], null)->subMonth()->endOfMonth();
        //         $billings = \App\Billing::where('payment_id', $payment->id)->whereBetween('expires_at', [ $prazo_inicial, $prazo_final ])->get();
        //     } else {
        //         continue;
        //     }
        // }





        return;

        \Artisan::call('CheckBillingsCommand');

        return;

        $api = new \App\Api\PlugBoleto();
        $billet = $api->getBoletos(\App\Billet::find(73));
        printa($billet);
        return;

        // $tests = \App\BillingTest::where('account_id', 2)->latest()->get();
        // $plugboleto = new \App\Api\PlugBoleto();

        // foreach ($tests as $key => $test) {
        //     $send = $test->billets->where('idIntegracao', '!=', null)->where('status', '!=', 'FALHA');
        //     if(!$send->count()) continue;
        //     $boletos = $plugboleto->getBoletosColecao($send);

        //     if(isset($boleto->_dados) && count($boleto->_dados)) {
        //         foreach ($boleto->_dados as $key => $dado) {
        //             if(!isset($dado->IdIntegracao)) continue;
        //             if(!$search = \Billet::where('idIntegracao', $dado->IdIntegracao)->first()) continue;

        //             $search->update([
        //                 'status'                => isset($dado->situacao)               ? $dado->situacao               : null,
        //                 'bar_code'              => isset($dado->TituloLinhaDigitavel)   ? $dado->TituloLinhaDigitavel   : null,
        //                 'PagamentoData'         => isset($dado->PagamentoData)          ? $dado->PagamentoData          : null,
        //                 'PagamentoRealizado'    => isset($dado->PagamentoRealizado)     ? $dado->PagamentoRealizado     : null,
        //                 'PagamentoValorPago'    => isset($dado->PagamentoValorPago)     ? $dado->PagamentoValorPago     : null,
        //             ]);

        //             if(isset($dado->motivo) && $dado->motivo) {
        //                 \App\BilletError::create([
        //                     'billet_id' => $search->id,
        //                     'message'   => $dado->motivo
        //                 ]);
        //             }
        //         }
        //     }

        //     printa($boletos);
        //     // foreach ($test->billets as $billet) {
        //     //     if($billet->status != 'FALHA' && $billet->idIntegracao) {
        //     // }
        // }

        // \Artisan::call('CheckBilletsCommand');

        return;

        // $billet = \App\Billet::find(20);

        // try {
        //     $billings = json_decode($billet->billetable->billings);

        //     $last_value = $billet->billetable->payment->generateAntecipation(1);

        //     foreach ($billings as $key => $b) {
        //         if(!$cobranca = \App\Billing::find($b)) continue;

        //         echo $last_value."<br>";

        //         // $cobranca->setStatus('PAID');
        //         $cobranca->update([ 'paid_value' => $last_value ]);

        //         $last_value = $last_value + ($last_value * $billet->billetable->payment->proposal->tax / 100);
        //     }
        // } catch (\Exception $e) { logging($e); }

        // return;

        // $this->math();
        // \Artisan::call('CreateBillingsCommand');
        // \Artisan::call('CheckBilletsCommand');

        return;

        $api = new \App\Api\PlugBoleto();

        $billet = $api->getBoletos(\App\Billet::find(144));
        //$billet = $api->getConvenio(\App\Agreement::find(1));
        printa($billet);

        return;

        \Artisan::call('CheckBilletsCommand');

        return;

        $pagamento = \App\Payment::find(25);

        $valor_devedor = $pagamento->billings->sum('value');
        $valor_amortizado = $pagamento->billings->whereIn('status', [ 'PAID', 'PAID_VALUE' ])->sum('amortization_value');

        echo "VALOR DEVEDOR NO MOMENTO: R$ ".formatMoney($valor_devedor)."<br>";
        echo "VALOR AMORTIZADO NO MOMENTO: R$ ".formatMoney($valor_amortizado)."<br>";
        echo "TAXA DE JUROS: ".$pagamento->proposal->tax."<br><br><br>";

        $valor_devedor = $valor_devedor - $valor_amortizado;

        foreach ($pagamento->billings->whereNotIn($status, [ 'PAID', 'PAID_MANUAL' ]) as $key => $billing) {
            $amortizacao = $billing->value - ($valor_devedor * $pagamento->proposal->tax) / 100;
            echo "VALOR PARCELA: R$ ".$billing->value." -- AMORTIZACAO: R$ ".formatMoney($amortizacao)."<br>";

            $billing->update([ 'amortization_value' => $amortizacao ]);

            $valor_devedor = $valor_devedor - $amortizacao;
        }

        return;

        $api = new \App\Api\PlugBoleto();

        // $billet = $api->getBoletos(\App\Billet::find(13));
        // printa($billet);

        $billet = $api->getBoletos(\App\Billet::find(18));
        printa($billet);

        return;

        \Artisan::call('CreateBillingsCommand');
        \Artisan::call('CheckBilletsCommand');

        return;

        for ($i = 0; $i < 6; $i++) {
            printa(getBillingToken(8));
        }

        return;

        return view('tests', $this->data);

        $proposals = \App\Proposal::where('status', 'SOLD')->get();

        foreach ($proposals as $key => $proposal) {
            if(!$proposal->payments->count()) continue;

            foreach ($proposal->payments as $key => $payment) {
                if(!$payment->method == 'Boleto') continue;

                echo "ID = ".$payment->id." | QUANTIDADE = ".$payment->quantity."<br>";

                switch ($payment->component) {
                    case 'Anual':           $param = 12;    break;
                    case 'Semestre':        $param = 6;     break;
                    case 'Trimestral':      $param = 3;     break;
                    case 'Bimestral':       $param = 2;     break;
                    case 'Mensal':
                    case 'Entrada/Sinal':   $param = 1;     break;
                    default:                $param = 1;     break;
                }

                $primeira_parcela = \Carbon\Carbon::parse($payment->expires_at);

                for ($i = 0; $i < $parcelas_restantes; $i++) {
                    $aux = $primeira_parcela;
                    $parcela = $aux->addMonths($param*$i);

                    \App\Billet::firstOrCreate(
                        [
                            'payment_id'    => $payment->id,
                            'month'         => $parcela->month,
                            'year'          => $parcela->year,
                        ],
                        [
                            'expires_at'    => null,
                            'bar_code'      => null,
                            'token'         => getBilletToken(6),
                            'value'         => null,
                        ]
                    );

                    /* CREATE BILLET REGISTER */
                    /* TRY TO CREATE BILLET IN PLUGBOLETO */
                }

                \App\Api\PlugBoleto::postBoleto($payment);
            }
        }

        return;

        $api = new \App\Api\PlugBoleto();
        $file = $api->imprimirPDF(\App\Proposal::find(3));

        // // var_dump($file);
        // $response = \Response::make($file, 200);
        // $response->header('Content-Type', 'application/pdf');
        // return $response;

        // return "AAAAAA";

        // $this->data['constructors'] = \App\Constructor::all();
        // $this->data['companies'] = \App\Company::all();
        // $this->data['owners'] = \App\Owner::where('status', 'ACTIVE')->get();

        // return view('projects.create2', $this->data);

        // $api = new \App\Api\PlugBoleto();
        // printa($api->getCedentes());

        // return;

        // echo '<pre>';

        // echo "123456789\n";
        // echo convert_number_to_words(123456789);
        // echo "\n\n";

        // echo "123456789.123\n";
        // echo convert_number_to_words(123456789.123);
        // echo "\n\n";

        // echo "-1922685.477\n";
        // echo convert_number_to_words(-1922685.477);
        // echo "\n\n";

        // echo "123456789123.12345\n";
        // echo convert_number_to_words(123456789123.12345); // rounds the fractional part
        // echo "\n\n";

        // echo "123456789123.12345\n";
        // echo convert_number_to_words('123456789123.12345'); // does not round

        // echo '</pre>';

        // $dados = array();
        // $doc = onlyNumber('02526456266');
        $doc = onlyNumber('11.086.416/0001-90');

        //$type = 'cpf';
        $type = 'cnpj';
        //$flow = '9e7f512f-d71b-49c3-afca-4b4f7186f2fc';
        $flow = '6f4fbd20-fe86-4c6c-af12-9ab4ed1a8666';
        // $dados['cpf'] = true;
        //$dados['cnpj'] = true;

        $engine = new \App\Api\DataEngine();
        $ret = $engine->call($flow, $type, $doc);

        printa($ret);

        if (!isset($ret->idCallManager)) {
            $dados['error'] = true;
            return;
        }

        $call = $ret->idCallManager;

        $try = 0;
        do {
            $ret = $engine->status($flow, $call);
            $try++;
        } while (!isset($ret->executionResult[0]->available) || $try == 5);

        printa($ret);

        printa($ret->executionResult[0]->observation);

        $info = json_decode($ret->executionResult[0]->observation);

        printa($info);

        // $companies_projects = CompanyProject::all();

        // foreach ($companies_projects as $key => $company_project) {
        //     $name = md5(uniqid(rand(), true)).'.pdf';
        //     $pdf = PDF::loadView('pdf.company_project_contract', ['project' => $company_project->project, 'company' => $company_project->company, 'company_project' => $company_project])->save(storage_path('app/public').'/'.$name);

        //     if ($pdf) {
        //         $company_project->update([
        //             'file' => $name
        //         ]);
        //     }

        // }

        // $users_projects = UserProject::all();

        // foreach ($users_projects as $key => $user_project) {

        //     $name = md5(uniqid(rand(), true)).'.pdf';
        //     $pdf = PDF::loadView('pdf.user_project_contract', ['project' => $user_project->project, 'user' => $user_project->user, 'user_project' => $user_project])->save(storage_path('app/public').'/'.$name);

        //     if ($pdf) {
        //         $user_project->update([
        //             'file' => $name
        //         ]);

        //         $mailer = new Mailer();
        //         $mailer->sendMailUserProjectContract($user_project->user->email, $user_project->user->name, $user_project);

        //         $user_project->update([
        //             'email_sent' => 1
        //         ]);
        //     }
        // }
    }

    public function store2(Request $request) {

        printa($request->all());

        if($request->has('select_owner') && is_array($request->select_owner) && count($request->select_owner)) {
            foreach ($request->select_owner as $key => $select_owner) {
                if(is_array($select_owner) && count($select_owner)) {
                    foreach ($select_owner as $so) {
                        \App\ProjectOwner::create([
                            'project_id'    => $project->id,
                            'owner_id'      => $key,
                            'account_id'    => $so
                        ]);
                    }
                }
            }
        }

    }

    public function math() {

        for ($i = 1; $i <= 24; $i++) {
            $valor_futuro = 0;
            $mensalidade = 470.73;
            $periodo = $i;
            $juros = 0.01;

            $valor_futuro = ( $valor_futuro / pow( ( 1 + $juros ), $periodo ) ) + ( $mensalidade * ( pow( ( 1 + $juros ), $periodo ) - 1 ) ) / ( pow( ( 1 + $juros ), ( $periodo + 1 ) ) - pow( ( 1 + $juros ), $periodo ) );

            $periodo = 24 - $i;
            $mensalidade = 0;

            $valor_presente = ( $valor_futuro / pow( ( 1 + $juros ), $periodo ) ) + ( $mensalidade * ( pow( ( 1 + $juros ), $periodo ) - 1 ) ) / ( pow( ( 1 + $juros ), ( $periodo + 1 ) ) - pow( ( 1 + $juros ), $periodo ) );

            printa("QTD: ".$i." -- ".$valor_presente);
        }
    }
}
