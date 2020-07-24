<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Company;
use App\Address;
use App\UserCompany;
use App\User;
use App\Mailer;

use Hash;

use Log;
use Exception;
use Auth;

class CompanyController extends Controller
{
    private $data = array();

    public function index() {
        // $this->data['companies'] = Company::all();
        switch (\Auth::user()->role) {
            case 'ADMIN': $this->data['companies'] = Company::all(); break;
            case 'INCORPORATOR':
                $projects = \Auth::user()->constructor->projects->pluck('id')->toArray();
                $company_ids = \App\CompanyProject::whereIn('project_id', $projects)->get()->pluck('company_id')->toArray();
                $this->data['companies'] = \App\Company::whereIn('id', $company_ids)->get();
            break;
            default: return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
        }

        return view('companies.index', $this->data);
    }

    public function create(Request $request) {
        if ($request->has('cnpj')) {
            $comp = Company::where('cnpj', $request->cnpj)->first();
            if ($comp) return redirect()->route('companies.index')->with('error', 'Já existe uma imobiliária cadastrada com o CNPJ informado.');
        } else {
            $this->data['cnpj'] = $cnpj;
        }

        return view('companies.create', $this->data);
    }

    public function store(Request $request) {
        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $comp = Company::where('cnpj', $request->cnpj)->first();

        if ($comp) return redirect()->route('companies.index')->with('error', 'Já existe uma imobiliária cadastrada com o CNPJ informado.');

        try {
            $company = Company::create([
                'name'      => $request->name,
                'creci'     => $request->creci,
                'cnpj'      => $request->cnpj,
                'manager'   => $request->manager,
                'email'     => $request->email,
                'telephone' => $request->telephone,
                'cellphone' => $request->cellphone
            ]);
        } catch(Exception $e) {
            logging($e);
            return redirect()->route('companies.index')->with('error', 'Não foi possível cadastrar a empresa.');
        }

        $user = null;
        try {
            $has = User::where('cpf', $request->cpf)->first();
            if (!$has) {
                $senha = uniqid();

                $user = User::create([
                    'role'          => 'COORDINATOR',
                    'cpf'           => $request->cpf,
                    'name'          => $request->manager,
                    'email'         => $request->email,
                    'password'      => Hash::make($senha),
                    'phone'         => $request->cellphone,
                    'creci'         => $request->creci_user,
                    'created_by'    => Auth::user()->id
                ]);
            }
        } catch (Exception $e) {
            logging($e);
        }

        if ($user) {
            try {
                $mailer = new Mailer();
                $mailer->sendMailUserCreate($request->email, $request->manager, $senha);
            } catch (Exception $e) {
                logging($e);
            }

            $user_company = UserCompany::create([
                'user_id' => $user->id,
                'company_id' => $company->id,
                'is_coordinator' => 1
            ]);
        }

        $admins = User::where('role', 'ADMIN')->get();
        foreach ($admins as $key => $admin) {
            UserCompany::firstOrCreate([
                'user_id' => $admin->id,
                'company_id' => $company->id,
                'is_coordinator' => 1
            ]);
        }

        $address_id = null;

        try {
            $address_id = Address::create([
                'is_billing' => 0,
                'zipcode'    => isset($request->zipcode) ? $request->zipcode : null,
                'street'     => isset($request->street) ? $request->street : null,
                'number'     => isset($request->number) ? $request->number : null,
                'complement' => isset($request->complement) ? $request->complement : null,
                'district'   => isset($request->district) ? $request->district : null,
                'city'       => isset($request->city) ? $request->city : null,
                'state'      => isset($request->state) ? $request->state : null,
            ])->id;
        } catch (Exception $e) {
            logging($e);
        }

        $company->address_id = $address_id;
        $company->save();

        return redirect()->route('companies.index')->with('success', 'Empresa cadastrada com sucesso.');
    }

    public function show($id) {
        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if (!Auth::user()->companies->contains('id', $id)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $company = Company::find($id);
        if (!$company) return redirect()->route('companies.index')->with('error', 'Empresa não encontrada.');

        $this->data['company'] = $company;
        return view('companies.show', $this->data);
    }

    public function edit($id) {
        $admins = User::where('role', 'ADMIN')->get();
        foreach ($admins as $key => $admin) {
            UserCompany::firstOrCreate([
                'user_id' => $admin->id,
                'company_id' => $id,
                'is_coordinator' => 1
            ]);
        }

        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if (!Auth::user()->companies->contains('id', $id)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $company = Company::find($id);
        if (!$company) return redirect()->route('companies.index')->with('error', 'Empresa não encontrada.');

        $this->data['company'] = $company;
        return view('companies.edit', $this->data);
    }

    public function update(Request $request, $id) {
        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if (!Auth::user()->companies->contains('id', $id)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            $company = Company::find($id);
            $company->update([
                'name'      => $request->name,
                'creci'     => $request->creci,
                'telephone' => $request->telephone,
            ]);

            try {
                if ($company->address) {
                    $company->address->update([
                        'zipcode'    => isset($request->zipcode) ? $request->zipcode : null,
                        'street'     => isset($request->street) ? $request->street : null,
                        'number'     => isset($request->number) ? $request->number : null,
                        'complement' => isset($request->complement) ? $request->complement : null,
                        'district'   => isset($request->district) ? $request->district : null,
                        'city'       => isset($request->city) ? $request->city : null,
                        'state'      => isset($request->state) ? $request->state : null,
                    ]);
                } else {
                    $address_id = Address::create([
                        'is_billing' => 0,
                        'zipcode'    => isset($request->zipcode) ? $request->zipcode : null,
                        'street'     => isset($request->street) ? $request->street : null,
                        'number'     => isset($request->number) ? $request->number : null,
                        'complement' => isset($request->complement) ? $request->complement : null,
                        'district'   => isset($request->district) ? $request->district : null,
                        'city'       => isset($request->city) ? $request->city : null,
                        'state'      => isset($request->state) ? $request->state : null,
                    ])->id;

                    $company->address_id = $address_id;
                    $company->save();
                }
            } catch (Exception $e) {
                logging($e);
            }
            return redirect()->route('companies.index')->with('success', 'Empresa editada com sucesso.');
        } catch(Exception $e) {
            logging($e);
            return redirect()->route('companies.index')->with('error', 'Não foi possível editar a empresa.');
        }
    }

    public function delete($id) {
        return redirect()->route('home')->with('error', 'Função em desenvolvimento.');

        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        if (!Auth::user()->companies->contains('id', $id)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            $company = Company::find($id);
            $company->delete();

            return redirect()->route('companies.index')->with('success', 'Empresa deletada com sucesso.');
        } catch(Exception $e) {
            logging($e);
            return redirect()->route('companies.index')->with('error', 'Não foi possível deletar a empresa.');
        }
    }
}