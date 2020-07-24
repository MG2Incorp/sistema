<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Property;
use App\Proposal;
use App\Proponent;
use App\Project;
use App\CompanyProject;
use App\UserCompany;
use App\Payment;
use App\Address;
use App\User;
use App\Block;
use App\Building;
use App\Document;
use App\Mailer;
use App\Contract;
use App\ProposalHistoryStatus;

use Log;
use Storage;
use Exception;
use Auth;

use Carbon\Carbon;

class ProposalController extends Controller
{
    private $data = array();

    public function __construct() {
        $this->data['breadcrumb'] = array();
        $this->data['breadcrumb'][] = ['text' => 'Home', 'is_link' => 1, 'link' => route('home')];
    }

    public function index(Request $request) {

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

        // echo "<div class='d-none'>".printa($filters)."</div>";

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
                    $propostas = $propostas->whereHas('main_proponent', function($query) use ($filter) {
                        $query->where('name', 'LIKE', '%'.$filter.'%');
                    });
                    // $has_proponent = true;
                    $prop = $filter;
                break;
                case 'start': case 'end':
                    $has_period = true;
                    $start = $filters['start'];
                    $end = $filters['end'];

                    $propostas = $propostas->whereBetween('created_at', [ \Carbon\Carbon::parse($filters['start'])->startOfDay(), \Carbon\Carbon::parse($filters['end'])->endOfDay() ]);

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

        // if($has_proponent) {
        //     $founds = $founds->reject(function($value, $key) use ($prop) { return strripos($value->main_proponent->name, $prop) === false; });
        // }

        $this->data['proposals'] = $founds;

        // switch (Auth::user()->role) {
        //     case 'ADMIN':
        //         // $this->data['proposals'] = Proposal::latest()->get();
        //         $this->data['proposals'] = Proposal::latest()->paginate(20);
        //     break;
        //     case 'INCORPORATOR':
        //         $projects = Project::where('constructor_id', Auth::user()->constructor_id)->get();

        //         $properties_ids = array();
        //         foreach ($projects as $key => $project) {
        //             $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
        //         }
        //         $properties_ids = array_unique($properties_ids);

        //         // $this->data['proposals'] = Proposal::whereIn('property_id', $properties_ids)->latest()->get();
        //         $this->data['proposals'] = Proposal::whereIn('property_id', $properties_ids)->latest()->paginate(20);
        //     break;
        //     case 'COORDINATOR':
        //         // $properties_ids = $users_ids = array();
        //         // $projects = Auth::user()->projects;
        //         // \Log::info("PROJECTS: ".json_encode(Auth::user()->companies->pluck('id')->toArray()));

        //         // foreach ($projects as $key => $project) {
        //         //     $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
        //         // }

        //         // foreach (Auth::user()->companies as $key => $company) {
        //         //     $users_ids = array_merge($users_ids, $company->users->pluck('id')->toArray());
        //         // }

        //         // $properties_ids = array_unique($properties_ids);
        //         // $users_ids = array_unique($users_ids);

        //         // \Log::info("USERS: ".json_encode($users_ids));

        //         // // $this->data['proposals'] = Proposal::whereNotIn('user_id', $users_ids)->whereIn('property_id', $properties_ids)->latest()->get();
        //         // $this->data['proposals'] = Proposal::whereIn('property_id', $properties_ids)->latest()->get();

        //         $companies = \Auth::user()->user_companies->where('is_coordinator', 1)->pluck('company_id')->toArray();

        //         $colegas = \App\UserCompany::whereIn('company_id', $companies)->get()->pluck('user_id')->toArray();

        //         $ids = \App\UserProject::where('user_id', \Auth::user()->id)->whereIn('company_id', $companies)->get()->pluck('project_id')->toArray();
        //         $projects = \App\Project::whereIn('id', $ids)->get();

        //         $properties_ids = [];
        //         foreach ($projects as $key => $project) {
        //             $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
        //         }

        //         // $this->data['proposals'] = \App\Proposal::whereIn('property_id', $properties_ids)->whereIn('user_id', $colegas)->latest()->get();
        //         $this->data['proposals'] = \App\Proposal::whereIn('property_id', $properties_ids)->whereIn('user_id', $colegas)->latest()->paginate(20);
        //     break;
        //     case 'AGENT':
        //         // $this->data['proposals'] = Proposal::where('user_id', Auth::user()->id)->latest()->get();
        //         $this->data['proposals'] = Proposal::where('user_id', Auth::user()->id)->latest()->paginate(20);
        //     break;
        //     default:
        //         $this->data['proposals'] = array();
        //     break;
        // }

        // if (Auth::user()->role == 'ADMIN') {
        //     $this->data['proposals'] = Proposal::all();
        // } else {
        //     $properties_ids = array();
        //     $projects = Auth::user()->projects;
        //     foreach ($projects as $key => $project) {
        //         $properties_ids = array_merge($properties_ids, $project->properties->pluck('id')->toArray());
        //     }
        //     $this->data['proposals'] = Proposal::whereIn('property_id', $properties_ids)->get();
        // }

        return view('proposals.index2', $this->data);
    }

    public function create(Request $request) {

        if (!$request->has('imovel')) return redirect()->route('map.index');

        $property = Property::find($request->imovel);
        if (!$property) return redirect()->route('map.index');

        if(!Auth::user()->checkPermission($property->block->building->project->id, ['PROPOSAL_CREATE'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $this->data['property'] = $property;

        if(Auth::user()->checkPermission($property->block->building->project->id, ['USER_SELECT'])) {
            $this->data['users'] = $property->block->building->project->users->reject(function($value, $key){
                return getRoleIndex($value->role) > getRoleIndex(Auth::user()->role);
            });
        }

        $indexes = explode(',', $property->block->building->project->indexes);
        $this->data['indexes'] = \App\MonetaryCorrectionIndex::whereIn('id', $indexes)->get();

        $this->data['contracts'] = $property->block->building->project->contracts;

        return view('proposals.create', $this->data);
    }

    public function store(Request $request) {

        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>";

        // return;

        $property = Property::find($request->property_id);
        if (!$property) return redirect()->back()->with('error', 'Imóvel não encontrado.')->withInput();
        if ($property->proposals_actives->count() == 3) return redirect()->back()->with('error', 'Número de propostas na fila para esse imóvel chegou no limite.')->withInput();
        if ($property->proposal_sold) return redirect()->back()->with('error', 'Esse imóvel já foi vendido.')->withInput();

        if(!Auth::user()->checkPermission($property->block->building->project->id, ['PROPOSAL_CREATE'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if(Auth::user()->role == "ADMIN") {
            $user = $request->user_id;
        } else {
            if (Auth::user()->hasUserPermission(['USER_SELECT', 'ADMIN', 'COORDINATOR'])) {
                $user = $request->user_id;
            } else {
                $user = Auth::user()->id;
            }
        }

        switch ($property->proposals_actives->count()) {
            case '0': $status = 'RESERVED'; break;
            case '1': $status = 'QUEUE_1'; break;
            case '2': $status = 'QUEUE_2'; break;
            default: $status = 'QUEUE_2'; break;
        }

        try {
            $proposal = Proposal::create([
                'property_id' => $request->property_id,
                'user_id' => $user,
                'media' => $request->media,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'modality' => $request->modality,
                'status' => $status,
                'correction_type' => isset($request->correction_type) && $request->correction_type != '0' ? $request->correction_type : null,
                'correction_index' => isset($request->correction_type) && $request->correction_type != '0' ? $request->correction_index == 'Outro' ? $request->other_correction_type : $request->correction_index : null,
                'discount' => toCoin($request->discount),
                'tax' => $property->block->building->project->fee
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível salvar a proposta. Motivo: '.$e->getMessage())->withInput();
        }

        try {
            $payments = $request->pay_componentes;

            $sum = 0;
            foreach ($payments as $i => $payment) {
            //for ($i = 0; $i < count($payments); $i++) {
                $sum += $request->pay_quantidades[$i]*toCoin($request->pay_valores[$i]);
            }

            foreach ($payments as $i => $payment) {
            // for ($i = 0; $i < count($payments); $i++) {
                $total = $request->pay_quantidades[$i]*toCoin($request->pay_valores[$i]);

                $pay = Payment::create([
                    'proposal_id' => $proposal->id,
                    'component' => $payments[$i],
                    'method' => $request->pay_metodos[$i],
                    'quantity' => $request->pay_quantidades[$i],
                    // 'expires_at' => dataToSQL($request->pay_validades[$i]),
                    'expires_at' => $request->pay_validades[$i],
                    'unit_value' => toCoin($request->pay_valores[$i]),
                    'percentage' => @$total/$sum*100,
                    'total_value' => $total
                ]);
            }
        } catch (Exception $e) {
            Log::info('ERRO AO SALVAR PAGAMENTO DA PROPOSTA. -> '.$e->getMessage()." -- Line: ".$e->getLine()." -- File: ".$e->getFile());
        }

        $check = $request->check_propo;

        for ($i = 1; $i < 5; $i++) {
            try {
                if (in_array($i, $check)) {
                    $address_id = $address_company_id = $address_spouse_company_id = null;

                    if (isset($request->zipcode[$i]) && strlen($request->zipcode[$i]) > 0) {
                        $address_id = Address::create([
                            'is_billing' => isset($request->billing_address) && in_array($i, $request->billing_address) ? 1 : 0,
                            'zipcode' => isset($request->zipcode[$i]) ? $request->zipcode[$i] : null,
                            'street' => isset($request->street[$i]) ? $request->street[$i] : null,
                            'number' => isset($request->number[$i]) ? $request->number[$i] : null,
                            'complement' => isset($request->complement[$i]) ? $request->complement[$i] : null,
                            'district' => isset($request->district[$i]) ? $request->district[$i] : null,
                            'city' => isset($request->city[$i]) ? $request->city[$i] : null,
                            'state' => isset($request->state[$i]) ? $request->state[$i] : null,
                        ])->id;
                    }

                    if (isset($request->company_zipcode[$i]) && strlen($request->company_zipcode[$i]) > 0) {
                        $address_company_id = Address::create([
                            'zipcode' => isset($request->company_zipcode[$i]) ? $request->company_zipcode[$i] : null,
                            'street' => isset($request->company_street[$i]) ? $request->company_street[$i] : null,
                            'number' => isset($request->company_number[$i]) ? $request->company_number[$i] : null,
                            'complement' => isset($request->company_complement[$i]) ? $request->company_complement[$i] : null,
                            'district' => isset($request->company_district[$i]) ? $request->company_district[$i] : null,
                            'city' => isset($request->company_city[$i]) ? $request->company_city[$i] : null,
                            'state' => isset($request->company_state[$i]) ? $request->company_state[$i] : null,
                        ])->id;
                    }

                    $proponent = Proponent::create([
                        'proposal_id' => $proposal->id,
                        'proponent_id' => null,
                        'main' => $request->main == $i ? 1 : 0,
                        'type' => isset($request->type[$i]) ? $request->type[$i] : null,
                        'name' => isset($request->name[$i]) ? strtoupper($request->name[$i]) : null,
                        'document' => isset($request->document[$i]) ? $request->document[$i] : null,
                        'proportion' => isset($request->proportion[$i]) ? toCoin($request->proportion[$i]) : 0,
                        'rg' => isset($request->rg[$i]) ? $request->rg[$i] : null,
                        'emitter' => isset($request->emitter[$i]) ? $request->emitter[$i] : null,
                        'rg_state' => isset($request->rg_state[$i]) ? $request->rg_state[$i] : null,
                        'email' => isset($request->email[$i]) ? $request->email[$i] : null,
                        'gender' => isset($request->gender[$i]) ? $request->gender[$i] : null,
                        // 'birthdate' => isset($request->birthdate[$i]) ? dataToSQL($request->birthdate[$i]) : null,
                        'birthdate' => isset($request->birthdate[$i]) ? $request->birthdate[$i] : null,
                        'phone' => isset($request->phone[$i]) ? $request->phone[$i] : null,
                        'cellphone' => isset($request->cellphone[$i]) ? $request->cellphone[$i] : null,
                        'mother_name' => isset($request->mother_name[$i]) ? $request->mother_name[$i] : null,
                        'father_name' => isset($request->father_name[$i]) ? $request->father_name[$i] : null,
                        'birthplace' => isset($request->birthplace[$i]) ? $request->birthplace[$i] : null,
                        'country' => isset($request->country[$i]) ? $request->country[$i] : null,
                        'house' => isset($request->house[$i]) ? $request->house[$i] : null,
                        'gross_income' => isset($request->gross_income[$i]) ? toCoin($request->gross_income[$i]) : null,
                        'net_income' => isset($request->net_income[$i]) ? toCoin($request->net_income[$i]) : null,
                        'occupation' => isset($request->occupation[$i]) ? $request->occupation[$i] : null,
                        'registry' => isset($request->registry[$i]) ? $request->registry[$i] : null,
                        'civil_status' => isset($request->civil_status[$i]) ? $request->civil_status[$i] : null,
                        'marriage' => isset($request->marriage[$i]) ? $request->marriage[$i] : null,
                        'company' => isset($request->company[$i]) ? $request->company[$i] : null,
                        'company_document' => isset($request->company_document[$i]) ? $request->company_document[$i] : null,
                        'role' => isset($request->role[$i]) ? $request->role[$i] : null,
                        // 'hired_at' => isset($request->hired_at[$i]) ? dataToSQL($request->hired_at[$i]) : null,
                        'hired_at' => isset($request->hired_at[$i]) ? $request->hired_at[$i] : null,
                        'company_phone' => isset($request->company_phone[$i]) ? $request->company_phone[$i] : null,
                        'company_cellphone' => isset($request->company_cellphone[$i]) ? $request->company_cellphone[$i] : null,
                        'address_id' => $address_id,
                        'company_address_id' => $address_company_id
                    ]);

                    if(isset($request->civil_status[$i]) && $request->civil_status[$i] == 'Casado') {
                        if (isset($request->spouse_company_zipcode[$i]) && strlen($request->spouse_company_zipcode[$i]) > 0) {
                            $address_spouse_company_id = Address::create([
                                'zipcode' => isset($request->spouse_company_zipcode[$i]) ? $request->spouse_company_zipcode[$i] : null,
                                'street' => isset($request->spouse_company_street[$i]) ? $request->spouse_company_street[$i] : null,
                                'number' => isset($request->spouse_company_number[$i]) ? $request->spouse_company_number[$i] : null,
                                'complement' => isset($request->spouse_company_complement[$i]) ? $request->spouse_company_complement[$i] : null,
                                'district' => isset($request->spouse_company_district[$i]) ? $request->spouse_company_district[$i] : null,
                                'city' => isset($request->spouse_company_city[$i]) ? $request->spouse_company_city[$i] : null,
                                'state' => isset($request->spouse_company_state[$i]) ? $request->spouse_company_state[$i] : null,
                            ])->id;
                        }

                        $spouse = Proponent::create([
                            'proposal_id' => $proposal->id,
                            'proponent_id' => $proponent->id,
                            'main' => null,
                            'name' => isset($request->spouse_name[$i]) ? strtoupper($request->spouse_name[$i]) : null,
                            'document' => isset($request->spouse_document[$i]) ? $request->spouse_document[$i] : null,
                            'proportion' => isset($request->spouse_proportion[$i]) ? toCoin($request->spouse_proportion[$i]) : null,
                            'rg' => isset($request->spouse_rg[$i]) ? $request->spouse_rg[$i] : null,
                            'emitter' => isset($request->spouse_emitter[$i]) ? $request->spouse_emitter[$i] : null,
                            'rg_state' => isset($request->spouse_rg_state[$i]) ? $request->spouse_rg_state[$i] : null,
                            'email' => isset($request->spouse_email[$i]) ? $request->spouse_email[$i] : null,
                            'gender' => isset($request->spouse_gender[$i]) ? $request->spouse_gender[$i] : null,
                            // 'birthdate' => isset($request->spouse_birthdate[$i]) ? dataToSQL($request->spouse_birthdate[$i]) : null,
                            'birthdate' => isset($request->spouse_birthdate[$i]) ? $request->spouse_birthdate[$i] : null,
                            'phone' => isset($request->spouse_phone[$i]) ? $request->spouse_phone[$i] : null,
                            'cellphone' => isset($request->spouse_cellphone[$i]) ? $request->spouse_cellphone[$i] : null,
                            'mother_name' => isset($request->spouse_mother_name[$i]) ? $request->spouse_mother_name[$i] : null,
                            'father_name' => isset($request->spouse_father_name[$i]) ? $request->spouse_father_name[$i] : null,
                            'birthplace' => isset($request->spouse_birthplace[$i]) ? $request->spouse_birthplace[$i] : null,
                            'country' => isset($request->spouse_country[$i]) ? $request->spouse_country[$i] : null,
                            'house' => null,
                            'gross_income' => isset($request->spouse_gross_income[$i]) ? toCoin($request->spouse_gross_income[$i]) : null,
                            'net_income' => isset($request->spouse_net_income[$i]) ? toCoin($request->spouse_net_income[$i]) : null,
                            'occupation' => isset($request->spouse_occupation[$i]) ? $request->spouse_occupation[$i] : null,
                            'registry' => isset($request->spouse_registry[$i]) ? $request->spouse_registry[$i] : null,
                            'civil_status' => null,
                            'marriage' => null,
                            'company' => isset($request->spouse_company[$i]) ? $request->spouse_company[$i] : null,
                            'company_document' => isset($request->spouse_company_document[$i]) ? $request->spouse_company_document[$i] : null,
                            'role' => isset($request->spouse_role[$i]) ? $request->spouse_role[$i] : null,
                            // 'hired_at' => isset($request->spouse_hired_at[$i]) ? dataToSQL($request->spouse_hired_at[$i]) : null,
                            'hired_at' => isset($request->spouse_hired_at[$i]) ? $request->spouse_hired_at[$i] : null,
                            'company_phone' => isset($request->spouse_company_phone[$i]) ? $request->spouse_company_phone[$i] : null,
                            'company_cellphone' => isset($request->spouse_company_cellphone[$i]) ? $request->spouse_company_cellphone[$i] : null,
                            'address_id' => null,
                            'company_address_id' => $address_spouse_company_id
                        ]);
                    }
                }
            } catch (Exception $e) {
                Log::info('ERRO AO SALVAR PROPONENTE DA PROPOSTA. -> '.$e->getMessage()." -- Line: ".$e->getLine()." -- File: ".$e->getFile());
            }
        }

        // dd();

        return redirect()->route('proposals.index')->with('success', 'Proposta salva com sucesso');
    }

    public function print(Request $request) {
        if (!$request->has('proposta')) return redirect()->route('proposals.index')->with('Proposta não encontrada.');

        $proposal = Proposal::find($request->proposta);

        if (!$proposal) return redirect()->route('proposals.index')->with('Proposta não encontrada.');

        $this->data['proposal'] = $proposal;
        $this->data['print'] = true;

        return view('proposals.print', $this->data);
    }

    public function see(Request $request) {
        if (!$request->has('proposta')) return redirect()->route('proposals.index')->with('Proposta não encontrada.');

        $proposal = Proposal::find($request->proposta);

        if (!$proposal) return redirect()->route('proposals.index')->with('Proposta não encontrada.');

        $this->data['proposal'] = $proposal;

        return view('proposals.print', $this->data);
    }

    public function edit($id) {
        $proposal = Proposal::find($id);

        if(!$proposal) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        if(!Auth::user()->checkPermission($proposal->property->block->building->project->id, ['PROPOSAL_EDIT'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if ($proposal->user_id != Auth::user()->id) {

            if(Auth::user()->role == 'INCORPORATOR') {
                if(Auth::user()->constructor_id != $proposal->property->block->building->project->constructor_id) {
                    return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
                }
            }

            if(Auth::user()->role == 'COORDINATOR') {
                $companies_ids = CompanyProject::where('project_id', $proposal->property->block->building->project->id)->get()->pluck('company_id')->toArray();
                $user_company = UserCompany::where('user_id', Auth::user()->id)->whereIn('company_id', $companies_ids)->where('is_coordinator', 1)->first();

                if(!$user_company) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
            }

            if(Auth::user()->role == 'AGENT') return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        }

        // if ($proposal->user_id != Auth::user()->id) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if(!in_array($proposal->status, ['RESERVED', 'DOCUMENTS_PENDING', 'REFUSED'])) return redirect()->back()->with('error', 'Essa proposta não pode ser editada no momento.');

        $this->data['proposal'] = $proposal;
        $this->data['contracts'] = $proposal->property->block->building->project->contracts;

        $indexes = explode(',', $proposal->property->block->building->project->indexes);
        $this->data['indexes'] = \App\MonetaryCorrectionIndex::whereIn('id', $indexes)->get();

        return view('proposals.edit', $this->data);
    }

    public function update(Request $request, $id) {

        // echo "<pre>";
        // print_r($request->all());
        // echo "</pre>";

        // return;

        $proposal = Proposal::find($id);

        if(!$proposal) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        if(!Auth::user()->checkPermission($proposal->property->block->building->project->id, ['PROPOSAL_EDIT'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if ($proposal->user_id != Auth::user()->id) {

            if(Auth::user()->role == 'INCORPORATOR') {
                if(Auth::user()->constructor_id != $proposal->property->block->building->project->constructor_id) {
                    return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
                }
            }

            if(Auth::user()->role == 'COORDINATOR') {
                $companies_ids = CompanyProject::where('project_id', $proposal->property->block->building->project->id)->get()->pluck('company_id')->toArray();
                $user_company = UserCompany::where('user_id', Auth::user()->id)->whereIn('company_id', $companies_ids)->where('is_coordinator', 1)->first();

                if(!$user_company) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
            }

            if(Auth::user()->role == 'AGENT') return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        }

        // if ($proposal->user_id != Auth::user()->id) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if(!in_array($proposal->status, ['RESERVED', 'DOCUMENTS_PENDING', 'REFUSED'])) return redirect()->back()->with('error', 'Essa proposta não pode ser editada no momento.');

        try {
            $proposal->update([
                'media' => $request->media,
                'reason' => $request->reason,
                'notes' => $request->notes,
                'modality' => $request->modality,
                'correction_type' => isset($request->correction_type) && $request->correction_type != '0' ? $request->correction_type : null,
                'correction_index' => isset($request->correction_type) && $request->correction_type != '0' ? $request->correction_index == 'Outro' ? $request->other_correction_type : $request->correction_index : null,
                'file' => null,
                'discount' => toCoin($request->discount)
            ]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Não foi possível editar a proposta. Motivo: '.$e->getMessage())->withInput();
        }

        try {
            $old_payments = $request->old_pay_componentes;
            $payments = $request->pay_componentes;

            $sum = 0;
            if(is_array($old_payments)) {
                foreach ($old_payments as $key => $old_payment) {
                    $sum += $request->old_pay_quantidades[$key]*toCoin($request->old_pay_valores[$key]);
                }
            }
            // for ($i = 0; $i < count($old_payments); $i++) { $sum += $request->old_pay_quantidades[$i]*toCoin($request->old_pay_valores[$i]); }
            if(is_array($payments)) {
                foreach ($payments as $i => $payment) {
                // for ($i = 0; $i < count($payments); $i++) {
                    $sum += $request->pay_quantidades[$i]*toCoin($request->pay_valores[$i]);
                }
            }

            if ($proposal->payments->count()) {
                foreach ($proposal->payments as $key => $payment) {
                    if (!isset($old_payments[$payment->id])) $payment->delete();
                }
            }

            if(is_array($old_payments)) {
                foreach ($old_payments as $key => $old_payment) {
                    $total = $request->old_pay_quantidades[$key]*toCoin($request->old_pay_valores[$key]);

                    $pay = Payment::where('id', $key)->where('proposal_id', $proposal->id)->first();
                    if ($pay) {
                        $pay->update([
                            'component' => $old_payment,
                            'method' => $request->old_pay_metodos[$key],
                            'quantity' => $request->old_pay_quantidades[$key],
                            // 'expires_at' => dataToSQL($request->old_pay_validades[$key]),
                            'expires_at' => $request->old_pay_validades[$key],
                            'unit_value' => toCoin($request->old_pay_valores[$key]),
                            'percentage' => @$total/$sum*100,
                            'total_value' => $total
                        ]);
                    }
                }
            }

            if(is_array($payments)) {
                foreach ($payments as $i => $payment) {
                // for ($i = 0; $i < count($payments); $i++) {
                    $total = $request->pay_quantidades[$i]*toCoin($request->pay_valores[$i]);

                    $pay = Payment::create([
                        'proposal_id' => $proposal->id,
                        'component' => $payments[$i],
                        'method' => $request->pay_metodos[$i],
                        'quantity' => $request->pay_quantidades[$i],
                        // 'expires_at' => dataToSQL($request->pay_validades[$i]),
                        'expires_at' => $request->pay_validades[$i],
                        'unit_value' => toCoin($request->pay_valores[$i]),
                        'percentage' => @$total/$sum*100,
                        'total_value' => $total
                    ]);
                }
            }
        } catch (Exception $e) {
            logging($e);
        }

        $old_propo = $request->proponent_id;

        foreach ($proposal->proponents as $key => $proponent) {
            if (!in_array($proponent->id, $old_propo)) {
                if ($proponent->proponent) {
                    if ($proponent->proponent->company_address) {
                        $proponent->proponent->company_address->delete();
                    }
                    $proponent->proponent->delete();
                }

                if ($proponent->company_address) $proponent->company_address->delete();
                if ($proponent->address) $proponent->address->delete();

                $proponent->delete();
            }
        }

        $check = $request->check_propo;

        for ($i = 1; $i < 5; $i++) {
            try {
                if (in_array($i, $check)) {
                    $address_id = $address_company_id = $address_spouse_company_id = $propo = null;

                    if ($old_propo[$i] != 0) {
                        $propo = Proponent::where('id', $old_propo[$i])->where('proposal_id', $proposal->id)->first();
                        if (!$propo) continue;
                    }

                    if (isset($request->zipcode[$i]) && strlen($request->zipcode[$i]) > 0) {
                        $ad = [
                            'is_billing' => isset($request->billing_address) && in_array($i, $request->billing_address) ? 1 : 0,
                            'zipcode' => isset($request->zipcode[$i]) ? $request->zipcode[$i] : null,
                            'street' => isset($request->street[$i]) ? $request->street[$i] : null,
                            'number' => isset($request->number[$i]) ? $request->number[$i] : null,
                            'complement' => isset($request->complement[$i]) ? $request->complement[$i] : null,
                            'district' => isset($request->district[$i]) ? $request->district[$i] : null,
                            'city' => isset($request->city[$i]) ? $request->city[$i] : null,
                            'state' => isset($request->state[$i]) ? $request->state[$i] : null
                        ];
                        if ($propo && $propo->address) {
                            $propo->address->update($ad);
                            $address_id = $propo->address->id;
                        } else {
                            $address_id = Address::create($ad)->id;
                        }
                    } else {
                        if ($propo && $propo->address) {
                            $propo->address->delete();
                            $address_id = null;
                        }
                    }

                    if (isset($request->company_zipcode[$i]) && strlen($request->company_zipcode[$i]) > 0) {
                        $ad = [
                            'zipcode' => isset($request->company_zipcode[$i]) ? $request->company_zipcode[$i] : null,
                            'street' => isset($request->company_street[$i]) ? $request->company_street[$i] : null,
                            'number' => isset($request->company_number[$i]) ? $request->company_number[$i] : null,
                            'complement' => isset($request->company_complement[$i]) ? $request->company_complement[$i] : null,
                            'district' => isset($request->company_district[$i]) ? $request->company_district[$i] : null,
                            'city' => isset($request->company_city[$i]) ? $request->company_city[$i] : null,
                            'state' => isset($request->company_state[$i]) ? $request->company_state[$i] : null
                        ];
                        if ($propo && $propo->company_address) {
                            $propo->company_address->update($ad);
                            $address_company_id = $propo->company_address->id;
                        } else {
                            $address_company_id = Address::create($ad)->id;
                        }
                    } else {
                        if ($propo && $propo->company_address) {
                            $propo->company_address->delete();
                            $address_company_id = null;
                        }
                    }

                    $p = [
                        'proposal_id' => $proposal->id,
                        'proponent_id' => null,
                        'main' => $request->main == $i ? 1 : 0,
                        'type' => isset($request->type[$i]) ? $request->type[$i] : null,
                        'name' => isset($request->name[$i]) ? $request->name[$i] : null,
                        'document' => isset($request->document[$i]) ? $request->document[$i] : null,
                        'proportion' => isset($request->proportion[$i]) ? toCoin($request->proportion[$i]) : 0,
                        'rg' => isset($request->rg[$i]) ? $request->rg[$i] : null,
                        'emitter' => isset($request->emitter[$i]) ? $request->emitter[$i] : null,
                        'rg_state' => isset($request->rg_state[$i]) ? $request->rg_state[$i] : null,
                        'email' => isset($request->email[$i]) ? $request->email[$i] : null,
                        'gender' => isset($request->gender[$i]) ? $request->gender[$i] : null,
                        // 'birthdate' => isset($request->birthdate[$i]) ? dataToSQL($request->birthdate[$i]) : null,
                        'birthdate' => isset($request->birthdate[$i]) ? $request->birthdate[$i] : null,
                        'phone' => isset($request->phone[$i]) ? $request->phone[$i] : null,
                        'cellphone' => isset($request->cellphone[$i]) ? $request->cellphone[$i] : null,
                        'mother_name' => isset($request->mother_name[$i]) ? $request->mother_name[$i] : null,
                        'father_name' => isset($request->father_name[$i]) ? $request->father_name[$i] : null,
                        'birthplace' => isset($request->birthplace[$i]) ? $request->birthplace[$i] : null,
                        'country' => isset($request->country[$i]) ? $request->country[$i] : null,
                        'house' => isset($request->house[$i]) ? $request->house[$i] : null,
                        'gross_income' => isset($request->gross_income[$i]) ? toCoin($request->gross_income[$i]) : null,
                        'net_income' => isset($request->net_income[$i]) ? toCoin($request->net_income[$i]) : null,
                        'occupation' => isset($request->occupation[$i]) ? $request->occupation[$i] : null,
                        'registry' => isset($request->registry[$i]) ? $request->registry[$i] : null,
                        'civil_status' => isset($request->civil_status[$i]) ? $request->civil_status[$i] : null,
                        'marriage' => isset($request->marriage[$i]) ? $request->marriage[$i] : null,
                        'company' => isset($request->company[$i]) ? $request->company[$i] : null,
                        'company_document' => isset($request->company_document[$i]) ? $request->company_document[$i] : null,
                        'role' => isset($request->role[$i]) ? $request->role[$i] : null,
                        // 'hired_at' => isset($request->hired_at[$i]) ? dataToSQL($request->hired_at[$i]) : null,
                        'hired_at' => isset($request->hired_at[$i]) ? $request->hired_at[$i] : null,
                        'company_phone' => isset($request->company_phone[$i]) ? $request->company_phone[$i] : null,
                        'company_cellphone' => isset($request->company_cellphone[$i]) ? $request->company_cellphone[$i] : null,
                        'address_id' => $address_id,
                        'company_address_id' => $address_company_id
                    ];

                    if ($propo) {
                        $propo->update($p);
                        $proponent = $propo;
                    } else {
                        $proponent = Proponent::create($p);
                    }

                    if(isset($request->civil_status[$i]) && $request->civil_status[$i] == 'Casado') {
                        if (isset($request->spouse_company_zipcode[$i]) && strlen($request->spouse_company_zipcode[$i]) > 0) {
                            $ad = [
                                'zipcode' => isset($request->spouse_company_zipcode[$i]) ? $request->spouse_company_zipcode[$i] : null,
                                'street' => isset($request->spouse_company_street[$i]) ? $request->spouse_company_street[$i] : null,
                                'number' => isset($request->spouse_company_number[$i]) ? $request->spouse_company_number[$i] : null,
                                'complement' => isset($request->spouse_company_complement[$i]) ? $request->spouse_company_complement[$i] : null,
                                'district' => isset($request->spouse_company_district[$i]) ? $request->spouse_company_district[$i] : null,
                                'city' => isset($request->spouse_company_city[$i]) ? $request->spouse_company_city[$i] : null,
                                'state' => isset($request->spouse_company_state[$i]) ? $request->spouse_company_state[$i] : null
                            ];

                            if ($propo && $propo->proponent && $propo->proponent->company_address) {
                                $propo->proponent->company_address->update($ad);
                                $address_spouse_company_id = $propo->proponent->company_address->id;
                            } else {
                                $address_spouse_company_id = Address::create($ad)->id;
                            }
                        } else {
                            if ($propo && $propo->proponent && $propo->proponent->company_address) {
                                $propo->proponent->company_address->delete();
                                $address_spouse_company_id = null;
                            }
                        }

                        $p = [
                            'proposal_id' => $proposal->id,
                            'proponent_id' => $proponent->id,
                            'main' => null,
                            'name' => isset($request->spouse_name[$i]) ? $request->spouse_name[$i] : null,
                            'document' => isset($request->spouse_document[$i]) ? $request->spouse_document[$i] : null,
                            'proportion' => isset($request->spouse_proportion[$i]) ? toCoin($request->spouse_proportion[$i]) : null,
                            'rg' => isset($request->spouse_rg[$i]) ? $request->spouse_rg[$i] : null,
                            'emitter' => isset($request->spouse_emitter[$i]) ? $request->spouse_emitter[$i] : null,
                            'rg_state' => isset($request->spouse_rg_state[$i]) ? $request->spouse_rg_state[$i] : null,
                            'email' => isset($request->spouse_email[$i]) ? $request->spouse_email[$i] : null,
                            'gender' => isset($request->spouse_gender[$i]) ? $request->spouse_gender[$i] : null,
                            // 'birthdate' => isset($request->spouse_birthdate[$i]) ? dataToSQL($request->spouse_birthdate[$i]) : null,
                            'birthdate' => isset($request->spouse_birthdate[$i]) ? $request->spouse_birthdate[$i] : null,
                            'phone' => isset($request->spouse_phone[$i]) ? $request->spouse_phone[$i] : null,
                            'cellphone' => isset($request->spouse_cellphone[$i]) ? $request->spouse_cellphone[$i] : null,
                            'mother_name' => isset($request->spouse_mother_name[$i]) ? $request->spouse_mother_name[$i] : null,
                            'father_name' => isset($request->spouse_father_name[$i]) ? $request->spouse_father_name[$i] : null,
                            'birthplace' => isset($request->spouse_birthplace[$i]) ? $request->spouse_birthplace[$i] : null,
                            'country' => isset($request->spouse_country[$i]) ? $request->spouse_country[$i] : null,
                            'house' => null,
                            'gross_income' => isset($request->spouse_gross_income[$i]) ? toCoin($request->spouse_gross_income[$i]) : null,
                            'net_income' => isset($request->spouse_net_income[$i]) ? toCoin($request->spouse_net_income[$i]) : null,
                            'occupation' => isset($request->spouse_occupation[$i]) ? $request->spouse_occupation[$i] : null,
                            'registry' => isset($request->spouse_registry[$i]) ? $request->spouse_registry[$i] : null,
                            'civil_status' => null,
                            'marriage' => null,
                            'company' => isset($request->spouse_company[$i]) ? $request->spouse_company[$i] : null,
                            'company_document' => isset($request->spouse_company_document[$i]) ? $request->spouse_company_document[$i] : null,
                            'role' => isset($request->spouse_role[$i]) ? $request->spouse_role[$i] : null,
                            // 'hired_at' => isset($request->spouse_hired_at[$i]) ? dataToSQL($request->spouse_hired_at[$i]) : null,
                            'hired_at' => isset($request->spouse_hired_at[$i]) ? $request->spouse_hired_at[$i] : null,
                            'company_phone' => isset($request->spouse_company_phone[$i]) ? $request->spouse_company_phone[$i] : null,
                            'company_cellphone' => isset($request->spouse_company_cellphone[$i]) ? $request->spouse_company_cellphone[$i] : null,
                            'address_id' => null,
                            'company_address_id' => $address_spouse_company_id
                        ];

                        if ($propo && $propo->proponent) {
                            $propo->proponent->update($p);
                        } else {
                            $proponent = Proponent::create($p);
                        }
                    } else {
                        if ($propo && $propo->proponent) $propo->proponent->delete();
                    }
                }
            } catch (Exception $e) {
                logging($e);
            }
        }

        // dd();
        //printa($request->all());

        return redirect()->route('proposals.index')->with('success', 'Proposta editada com sucesso.');
    }

    public function status(Request $request) {
        if (!$request->has('proposta')) return redirect()->route('proposals.index')->with('error', 'Proposta não encontrada.');
        if (!$request->has('status') || !in_array($request->status, getProposalStatus())) return redirect()->route('proposals.index')->with('error', 'Status não existente.');

        $proposal = Proposal::find($request->proposta);

        if (!$proposal) return redirect()->route('proposals.index')->with('error', 'Proposta não encontrada.');

        if (in_array($proposal->status, ['QUEUE_1', 'QUEUE_2'])) return redirect()->route('proposals.index')->with('error', 'Essa proposta ainda está na fila de espera, portanto seu status não pode ser alterado.');

        if (!in_array($request->status, getStatusByRole(Auth::user()->role))) return redirect()->route('proposals.index')->with('error', 'Você não permissão para completar a operação.');

        // if ($request->status == 'SOLD' && !Auth::user()->checkPermission($project->id, ['PROPERTY_SOLD'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        // if ($request->status == 'CANCELED' && !Auth::user()->checkPermission($project->id, ['PROPOSAL_DELETE'])) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        // if ($request->status == '') {
        //     $has = Proposal::where('property_id', $proposal->property_id)->where('status', '>', 0)->where('id', '!=', $proposal->id)->first();
        //     if ($has) return redirect()->route('proposals.index')->with('error', 'Não foi possível alterar o status dessa proposta, pois já existe uma outra proposta em andamento para esse imóvel.');
        // }

        if ($request->status == 'CANCELED') {
            if(!\Hash::check($request->password, \Auth::user()->password)) return redirect()->back()->with('error', 'Senha incorreta.');
        }

        if ($request->status == 'RESERVED') {
            $property = $proposal->property;

            $others = $property->proposals_actives->where('id', '!=', $proposal->id);
            if ($others->count() != 0 && !$property->proposal_sold) {
                return redirect()->route('proposals.index')->with('error', 'Existem outras propostas ativas para esse imóvel, portanto não é possível alterar o status requisitado.');
            }
        }

        if ($request->status == 'QUEUE_1') {
            $property = $proposal->property;

            $others = $property->proposals->where('id', '!=', $proposal->id);
            if ($others->count() == 0) {
                return redirect()->route('proposals.index')->with('error', 'Não foi possível alterar o status requisitado.');
            }
        }

        if ($request->status == 'QUEUE_2') {
            $property = $proposal->property;

            $others = $property->proposals->where('id', '!=', $proposal->id);
            if ($others->count() == 0) {
                return redirect()->route('proposals.index')->with('error', 'Não foi possível alterar o status requisitado.');
            } elseif ($others->count() == 1) {
                return redirect()->route('proposals.index')->with('error', 'Não foi possível alterar o status requisitado.');
            }
        }

        $proposal->setStatus($request->status, $request->notes);

        try {
            $mailer = new Mailer();
            switch ($proposal->status) {
                case 'RESERVED':
                    $proposal->created_at = Carbon::now();
                    $proposal->save();

                    $mailer = new Mailer();
                    foreach ($proposal->property->block->building->project->constructor->users->where('receive_emails', 1) as $user) {
                        $mailer->sendMailProposalStatus($proposal->status, $user, $proposal);
                    }
                break;
                case 'PROPOSAL':

                break;
                case 'PROPOSAL_REVIEW':

                break;
                case 'DOCUMENTS_REVIEW':

                break;
                case 'DOCUMENTS_PENDING':
                    $mailer = new Mailer();
                    $mailer->sendMailProposalStatus($proposal->status, $proposal->user, $proposal);
                break;
                case 'CONTRACT_ISSUE':
                    if($request->has('modality') && $request->modality) $proposal->update([ 'modality' => $request->modality ]);

                    $proposal->generateContract();
                break;
                case 'CONTRACT_AVAILABLE':
                    $mailer = new Mailer();
                    $mailer->sendMailProposalStatus($proposal->status, $proposal->user, $proposal);
                break;
                case 'PENDING_SIGNATURE_CLIENT':

                break;
                case 'PENDING_SIGNATURE_CONSTRUCTOR':

                break;
                case 'SOLD':

                break;
                case 'REFUSED':
                    $mailer = new Mailer();
                    $mailer->sendMailProposalStatus($proposal->status, $proposal->user, $proposal);
                break;
                case 'CANCELED':
                    $proposal->update([ 'file' => null ]);
                break;
                case 'QUEUE_1':

                break;
                case 'QUEUE_2':

                break;
                default:

                break;
            }
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->back()->with('success', 'Status alterado com sucesso');
    }

    public function document(Request $request) {

        // printa($request->all());

        // return;

        //if(!in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) return redirect()->back()->with('error', 'Você não tem permissão necessária para completar essa operação.');

        $proposal = Proposal::find($request->proposal_id);

        $count = 0;
        foreach ($proposal->proponents as $key => $proponent) {
            if ($proponent->proponent) {
                $count = $count + 12;
            } else {
                $count = $count + 5;
            }
        }

        $arquivos = 0;

        if ($request->has('other_documents')) {
            foreach ($request->other_documents as $key => $document) {
                foreach ($document['outro_name'] as $i => $outro_name) {
                    try {
                        if(isset($document['outro_file'][$i])) {
                            $file = $document['outro_file'][$i];
                            $ext = $file->getClientOriginalExtension();

                            $formats = ['gif', 'bmp', 'png', 'jpg', 'jpeg', 'pdf', 'rar', 'zip', 'html', 'txt', 'tar', 'docx'];

                            if(in_array($ext, $formats)) {
                                $tmp_name = $file->getPathName();
                                $name = md5(uniqid(rand(), true));

                                $filename = sprintf(env('DOCUMENTS_DIR').'%s.%s', $name, $ext);

                                $t = Storage::put($filename, file_get_contents($tmp_name));

                                if ($t) {
                                    Document::create([
                                        'user_id' => Auth::user()->id,
                                        'proposal_id' => $request->proposal_id,
                                        'proponent_id' => $key,
                                        'file' => $name.".".$ext,
                                        'type' => 'other',
                                        'text' => $outro_name
                                    ]);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        logging($e);
                    }
                }
            }
        }

        if ($request->has('documents')) {
            foreach ($request->documents as $key => $document) {
                foreach ($document as $i => $doc) {
                    try {
                        $file = $doc;
                        $ext = $file->getClientOriginalExtension();

                        $formats = ['gif', 'bmp', 'png', 'jpg', 'jpeg', 'pdf', 'rar', 'zip', 'html', 'txt', 'tar', 'docx'];

                        if(in_array($ext, $formats)) {
                            $tmp_name = $file->getPathName();
                            $name = md5(uniqid(rand(), true));

                            $filename = sprintf(env('DOCUMENTS_DIR').'%s.%s', $name, $ext);

                            $t = Storage::put($filename, file_get_contents($tmp_name));

                            if ($t) {
                                $doc = Document::where('proposal_id', $request->proposal_id)->where('proponent_id', $key)->where('type', $i)->first();
                                if (!$doc) {
                                    Document::create([
                                        'user_id' => Auth::user()->id,
                                        'proposal_id' => $request->proposal_id,
                                        'proponent_id' => $key,
                                        'file' => $name.".".$ext,
                                        'type' => $i
                                    ]);
                                } else {
                                    $doc->update([
                                        'file' => $name.".".$ext
                                    ]);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        logging($e);
                    }
                }
            }

            if ($proposal->documents->count() == $count) {
                $proposal->setStatus('PROPOSAL_REVIEW', 'Status alterado para "'.getProposalStatusName('PROPOSAL_REVIEW').'" devido a inserção de documentação completa."');
            } else {
                $proposal->setStatus('DOCUMENTS_PENDING', 'Status alterado para "'.getProposalStatusName('DOCUMENTS_PENDING').'" devido a inserção de documentação faltante."');
            }

            return redirect()->back()->with('success', 'Documentos salvos com sucesso.');
        }

        if (!$request->has('other_documents') && !$request->has('documents')) return redirect()->back()->with('error', 'Nenhum documento selecionado.');

        return redirect()->back();
    }

    public function download($file) {
        return response()->file(storage_path('app').'/'.env('DOCUMENTS_DIR').$file);
        return Storage::download(env('DOCUMENTS_DIR').$file);
    }

    public function search(Request $request) {

        $dados = array();

        $doc = onlyNumber($request->doc);

        if (strlen($doc) == 11) {
            $type = 'cpf';
            $flow = '9e7f512f-d71b-49c3-afca-4b4f7186f2fc';
            $dados['cpf'] = true;
        } elseif (strlen($doc) == 14) {
            $type = 'cnpj';
            $flow = '6f4fbd20-fe86-4c6c-af12-9ab4ed1a8666';
            $dados['cpnj'] = true;
        } else {
            $dados['error'] = true;
            return collect($dados);
        }

        if(!strlen($doc)) {
            $dados['error'] = true;
            return collect($dados);
        }

        $engine = new \App\Api\DataEngine();

        // $ret = $engine->auth();

        // \Log::info('AUTH: '.serialize($ret));

        // if (!isset($ret->access_token)) {
        //     $dados['error'] = true;
        //     return collect($dados);
        // }

        // $access = $ret->access_token;

        //$ret = $engine->call($access, $flow, $type, $doc);
        $ret = $engine->call($flow, $type, $doc);

        \Log::info('CALL: '.serialize($ret));

        if (!isset($ret->idCallManager)) {
            $dados['error'] = true;
            return collect($dados);
        }

        $call = $ret->idCallManager;

        $try = 0;
        do {
            $ret = $engine->status($flow, $call);
            \Log::info('STATUS: '.serialize($ret));

            $try++;
        } while (!isset($ret->executionResult[0]->available) || $try <= 5);

        if (!isset($ret->executionResult[0]->available)) {
            $dados['error'] = true;
            return collect($dados);
        }

        $info = json_decode($ret->executionResult[0]->observation);

        Log::info(@serialize($info));

        //$dados['error'] = true;
        //return collect($dados);

        try {
            if($type == 'cpf') {
                $dados['name'] = ucwords(strtolower($info->Result[0]->BasicData->Name));
                $dados['gender'] = $info->Result[0]->BasicData->Gender == 'M' ? 'Masculino' : 'Feminino';
                $dados['birthdate'] = dateString(dateTimeString($info->Result[0]->BasicData->BirthDate));
                $dados['mother_name'] = ucwords(strtolower($info->Result[0]->BasicData->MotherName));
            } else {
                // $dados['name'] = ucwords(strtolower($info[0]->SintegraData->DefaultData->SocialReason));
                // $dados['email'] = $info[0]->SintegraData->AdditionalInformation->Email;
                // $dados['zipcode'] = mask("#####-###", onlyNumber($info[0]->SintegraData->AddressInformation->Cep));
                // $dados['state'] = $info[0]->SintegraData->AddressInformation->Uf;
                // $dados['city'] = ucwords(strtolower($info[0]->SintegraData->AddressInformation->District));
                // $dados['district'] = ucwords(strtolower($info[0]->SintegraData->AddressInformation->Neighborhood));
                // $dados['street'] = ucwords(strtolower($info[0]->SintegraData->AddressInformation->Address));
                // $dados['number'] = $info[0]->SintegraData->AddressInformation->Number;
                // $dados['complement'] = ucwords(strtolower($info[0]->SintegraData->AddressInformation->Complement));
                // $dados['telephone'] = $info[0]->SintegraData->AddressInformation->Phone;

                /* MUDOU - 15/08/2019 */
                // $dados['name'] = ucwords(strtolower($info->nome));
                // $dados['email'] = $info->email;
                // $dados['zipcode'] = mask("#####-###", onlyNumber($info->cep));
                // $dados['state'] = $info->uf;
                // $dados['city'] = ucwords(strtolower($info->municipio));
                // $dados['district'] = ucwords(strtolower($info->bairro));
                // $dados['street'] = ucwords(strtolower($info->logradouro));
                // $dados['number'] = $info->numero;
                // $dados['complement'] = '';
                // $dados['telephone'] = $info->telefone;

                $dados['name'] = ucwords(strtolower($info->nome_empresarial));
                $dados['zipcode'] = mask("#####-###", onlyNumber($info->cep));
                $dados['state'] = $info->uf;
                $dados['city'] = ucwords(strtolower($info->municipio));
                $dados['district'] = ucwords(strtolower($info->bairro));
                $dados['street'] = ucwords(strtolower($info->logradouro));
                $dados['number'] = $info->numero;
                $dados['complement'] = $info->complemento;
            }

            return collect($dados);
        } catch(Exception $e) {
            Log::info('File: '.$e->getFile().' -- Line: '.$e->getLine().' -- Message: '.$e->getMessage());

            $dados['error'] = true;
            return collect($dados);
        }
    }

    public function deleteDocument(Request $request, $id) {

        //if(!in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) return redirect()->back()->with('error', 'Você não tem permissão necessária para completar essa operação.');

        try {
            $document = Document::where('proposal_id', $request->proposta)->where('id', $id)->first();
            if ($document) {
                $document->delete();
                return redirect()->back()->with('success', 'Documento deletado com sucesso');
            }
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->back()->with('error', 'Não foi possível deletar o documento.');
    }

    public function loadStatus(Request $request) {

        $proposal = Proposal::find($request->id);

        if(!$proposal) return 'Você não tem permissão para realizar essa operação.';

        if ($proposal->user_id != Auth::user()->id) {
            if(Auth::user()->role == 'INCORPORATOR') {
                if(Auth::user()->constructor_id != $proposal->property->block->building->project->constructor_id) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'COORDINATOR') {
                $companies_ids = CompanyProject::where('project_id', $proposal->property->block->building->project->id)->get()->pluck('company_id')->toArray();
                $user_company = UserCompany::where('user_id', Auth::user()->id)->whereIn('company_id', $companies_ids)->where('is_coordinator', 1)->first();

                if(!$user_company) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'AGENT') return 'Você não tem permissão para realizar essa operação.';
        }

        $h = '<form method="POST" action="'.action('ProposalController@status').'" id="form_change_status">
                    '.csrf_field().'
                    <div class="form-group">
                        <input type="hidden" name="proposta" value="'.$proposal->id.'">
                        <label>Novo status</label>
                        <select name="status" class="form-control selectpicker select_status">
                            <option value="">Selecione...</option>';
                            foreach(getProposalStatus() as $status) {
                                if($proposal->status != getProposalStatusName($status) && in_array($status, getStatusByRole(\Auth::user()->role))) {
                                    if(!in_array($status, [ 'QUEUE_1', 'QUEUE_2' ])) {
                                        $h .= '<option value="'.$status.'">'.getProposalStatusName($status).'</option>';
                                    }
                                }
                            }
                  $h .= '</select>
                    </div>

                    <div class="form-group div_modality d-none">
                        <label>Selecione o template do contrato</label>
                        <select name="modality" class="form-control">';
                            foreach($proposal->property->block->building->project->contracts as $contract) {
                                $h .= '<option value="'.$contract->id.'" '.($contract->id == $proposal->modality ? 'selected' : '').'>'.$contract->name.'</option>';
                            }
                 $h .= '</select>
                    </div>

                    <div class="form-group">
                        <label>Observações</label>
                        <textarea name="notes" class="form-control" rows="5" style="resize: none"></textarea>
                    </div>

                    <div class="form-group d-none" id="require_pass">
                        <label>Para cancelar essa proposta e/ou venda, informe sua senha abaixo (após o cancelamento só será possível sua visualização):</label>
                        <input type="password" name="password" value="" class="form-control" id="input_require_pass">
                    </div>

                    <button type="submit" class="btn btn-success btn-sm float-right">Alterar</button>
                </form>';

        return $h;
    }

    public function loadHistory(Request $request) {
        $proposal = Proposal::find($request->id);

        if(!$proposal) return 'Você não tem permissão para realizar essa operação.';

        if ($proposal->user_id != Auth::user()->id) {
            if(Auth::user()->role == 'INCORPORATOR') {
                if(Auth::user()->constructor_id != $proposal->property->block->building->project->constructor_id) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'COORDINATOR') {
                $companies_ids = CompanyProject::where('project_id', $proposal->property->block->building->project->id)->get()->pluck('company_id')->toArray();
                $user_company = UserCompany::where('user_id', Auth::user()->id)->whereIn('company_id', $companies_ids)->where('is_coordinator', 1)->first();

                if(!$user_company) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'AGENT') return 'Você não tem permissão para realizar essa operação.';
        }

        $aux = '';
        foreach($proposal->statuses->sortByDesc('created_at') as $status) {
            switch($status->status) {
                case 'RESERVED': case 'QUEUE_1': case 'QUEUE_2': $color = 'warning'; break;
                case 'PROPOSAL': case 'PROPOSAL_REVIEW': case 'DOCUMENTS_PENDING': case 'DOCUMENTS_REVIEW': $color = 'secondary'; break;
                case 'CONTRACT_ISSUE': $color = 'info'; break;
                case 'CONTRACT_AVAILABLE': $color = 'primary'; break;
                case 'PENDING_SIGNATURE_CLIENT': case 'PENDING_SIGNATURE_CONSTRUCTOR': case 'SOLD': $color = 'success'; break;
                case 'REFUSED': case 'CANCELED': $color = 'danger'; break;
            }
            $aux .= '<tr>
                        <td>'.dateString($status->created_at).'</td>
                        <td><h5><span class="badge badge-'.@$color.'">'.getProposalStatusName($status->status).'</span></h5></td>
                        <td>'.$status->user->name.'</td>
                        <td>'.$status->notes.'</td>
                    </tr>';
        }

        return '<table class="table table-sm table-bordered text-center m-0">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Status</th>
                            <th>Responsável</th>
                            <th>Observações</th>
                        </tr>
                    </thead>
                    <tbody>'.$aux.'</tbody>
                </table>';
    }

    public function loadDocs(Request $request) {

    }

    public function loadEmails(Request $request) {
        $proposal = Proposal::find($request->id);

        if(!$proposal) return 'Você não tem permissão para realizar essa operação.';

        if ($proposal->user_id != Auth::user()->id) {
            if(Auth::user()->role == 'INCORPORATOR') {
                if(Auth::user()->constructor_id != $proposal->property->block->building->project->constructor_id) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'COORDINATOR') {
                $companies_ids = CompanyProject::where('project_id', $proposal->property->block->building->project->id)->get()->pluck('company_id')->toArray();
                $user_company = UserCompany::where('user_id', Auth::user()->id)->whereIn('company_id', $companies_ids)->where('is_coordinator', 1)->first();

                if(!$user_company) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'AGENT') return 'Você não tem permissão para realizar essa operação.';
        }

        $aux = '';
        foreach($proposal->proponents as $proponent) {
            $aux .= '<option value="'.$proponent->id.'">'.$proponent->name.'</option>';
        }

        return '<input type="hidden" name="type" value="EMAIL">
                <input type="hidden" name="proposal_id" value="'.$proposal->id.'">
                <div class="form-group">
                    <label>Selecione os proponents que deseja enviar o contrato</label>
                    <select class="form-control js-choice select_users" name="users[]" multiple>'.$aux.'</select>
                </div>
                <div class="form-group">
                    <label>E-Mails adicionais</label>
                    <input type="text" name="emails" class="tags">
                </div>
                <div class="form-group">
                    <label>Resultado do processamento</label>
                    <div id="resultado-form_send_contract_email"></div>
                </div>';
    }

    public function loadWhatsApp(Request $request) {

        $proposal = Proposal::find($request->id);

        if(!$proposal) return 'Você não tem permissão para realizar essa operação.';

        if ($proposal->user_id != Auth::user()->id) {
            if(Auth::user()->role == 'INCORPORATOR') {
                if(Auth::user()->constructor_id != $proposal->property->block->building->project->constructor_id) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'COORDINATOR') {
                $companies_ids = CompanyProject::where('project_id', $proposal->property->block->building->project->id)->get()->pluck('company_id')->toArray();
                $user_company = UserCompany::where('user_id', Auth::user()->id)->whereIn('company_id', $companies_ids)->where('is_coordinator', 1)->first();

                if(!$user_company) return 'Você não tem permissão para realizar essa operação.';
            }

            if(Auth::user()->role == 'AGENT') return 'Você não tem permissão para realizar essa operação.';
        }

        $aux = '';
        foreach($proposal->proponents as $proponent) {
            $aux .= '<option value="'.$proponent->id.'">'.$proponent->name.' - '.$proponent->cellphone.'</option>';
        }

        return '<input type="hidden" name="type" value="PHONE">
                <input type="hidden" name="proposal_id" value="'.$proposal->id.'">
                <div class="form-group">
                    <label>Selecione os proponents que deseja enviar o contrato</label>
                    <select class="form-control js-choice select_users" name="users[]" multiple>'.$aux.'</select>
                </div>
                <div class="form-group">
                    <label>Telefones adicionais</label>
                    <input type="text" name="phones" class="tags">
                </div>
                <div class="form-group">
                    <label>Resultado do processamento</label>
                    <div id="resultado-form_send_contract_phone"></div>
                </div>';
    }

}
