<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Company;
use App\UserProject;
use App\UserPermission;
use App\Project;
use App\Permission;
use App\Mailer;
use App\UserAttach;
use App\UserCompany;
use App\Constructor;

use Session;
use Hash;
use Exception;
use Log;
use Auth;
use PDF;

class UserController extends Controller
{
    private $data = array();

    public function __construct() {

    }

    public function index(Request $request) {
        $this->data['companies'] = Auth::user()->companies;

        $this->data['avulsos'] = User::whereHas('projects', function($query) {
            $query->where('company_id', 0);
        })->orWhereNull('company_id')->get();

        return view('users.index2', $this->data);
    }

    public function create(Request $request) {

        if (!$request->has('imobiliaria')) return redirect()->route('users.index')->with('error', 'Não foi possível completar a operação.');
        if (!$request->has('cpf')) return redirect()->route('users.index')->with('error', 'Não foi possível completar a operação.');

        $company = Company::find($request->imobiliaria);
        if (!$company) return redirect()->route('users.index')->with('error', 'Não foi possível completar a operação.');

        $this->data['permissions'] = Permission::all();
        $this->data['projects'] = Auth::user()->projects;

        $this->data['cpf'] = $request->cpf;
        $this->data['company'] = $request->imobiliaria;

        if (Auth::user()->cpf == $request->cpf) return redirect()->route('users.index')->with('error', 'Esse CPF pertence a você.');

        $user = User::where('cpf', $request->cpf)->where('id', '!=', Auth::user()->id)->first();

        if (Auth::user()->role == 'ADMIN') {
            $this->data['constructors'] = Constructor::all();
        }

        if (!$user) return view('users.create2', $this->data);

        $user_company = UserCompany::where('user_id', $user->id)->where('company_id', $request->imobiliaria)->first();
        if ($user_company) return redirect()->route('users.edit', [$user->id, 'imobiliaria' => $request->imobiliaria])->with('error', 'Esse usuário já está vinculado a essa imobiliaria. Você pode editá-lo.');

        if (getRoleIndex($user->role) > getRoleIndex(Auth::user()->role)) return redirect()->route('users.index')->with('error', 'Usuário encontrado mas possui um nível de permissão maior do que o seu.');

        $user_company = UserCompany::create([
            'user_id' => $user->id,
            'company_id' => $request->imobiliaria,
            'is_coordinator' => 0
        ]);

        return redirect()->route('users.edit', [$user->id, 'imobiliaria' => $request->imobiliaria])->with('success', 'Usuário associado com sucesso.');

        //$this->data['user'] = $user;
        //return view('users.attach2', $this->data);
    }

    public function create2(Request $request) {
        if (!$request->has('cpf')) return redirect()->route('users.index')->with('error', 'Não foi possível completar a operação.');

        $this->data['permissions'] = Permission::all();
        $this->data['projects'] = Auth::user()->projects;

        $this->data['cpf'] = $request->cpf;

        if (Auth::user()->cpf == $request->cpf) return redirect()->route('users.index')->with('error', 'Esse CPF pertence a você.');

        $user = User::where('cpf', $request->cpf)->where('id', '!=', Auth::user()->id)->first();

        if (Auth::user()->role == 'ADMIN') {
            $this->data['constructors'] = Constructor::all();
        }

        if (!$user) return view('users.create3', $this->data);
    }

    public function attach(Request $request) {

        if (!$request->has('user_id')) return redirect()->route('home')->with('error', 'Não foi possível completar a operação.');
        if (!$request->has('company')) return redirect()->route('home')->with('error', 'Não foi possível completar a operação.');

        $user = User::find($request->user_id);
        if (!$user) return redirect()->route('home')->with('error', 'Não foi possível completar a operação.');

        $company = Company::find($request->company);
        if (!$company) return redirect()->route('home')->with('error', 'Não foi possível completar a operação.');

        if ($request->user_id == Auth::user()->id) return redirect()->route('home')->with('error', 'Não foi possível completar a operação.');

        $user_company = UserCompany::where('user_id', $request->user_id)->where('company_id', $request->company)->first();
        if ($user_company) return redirect()->route('users.edit', [$request->user_id, 'imobiliaria' => $request->company])->with('error', 'Esse usuário já está vinculado a essa imobiliaria. Você pode editá-lo.');

        $user_company = UserCompany::create([
            'user_id' => $request->user_id,
            'company_id' => $request->company,
            'is_coordinator' => 0
        ]);

        return redirect()->route('users.edit', [$request->user_id, 'imobiliaria' => $request->company])->with('success', 'Usuário associado com sucesso.');
    }

    public function store(Request $request) {
        if (getRoleIndex($request->role) > getRoleIndex(Auth::user()->role)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $user = User::where('cpf', $request->cpf)->first();

        if ($user) return redirect()->back()->with('error', 'Já existe um usuário cadastrado com esse CPF.');

        if (!Auth::user()->companies->contains('id', $request->company)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            $construct = null;

            if ($request->role == 'INCORPORATOR') {
                if ($request->has('constructor') && Auth::user()->role == 'ADMIN') {
                    $construct = $request->constructor;
                } elseif (Auth::user()->constructor_id != null) {
                    $construct = Auth::user()->constructor_id;
                }
            }

            // $senha = uniqid();
            $senha = onlyNumber($request->cpf);

            $user = User::create([
                'role'              => $request->role,
                'cpf'               => $request->cpf,
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($senha),
                'company_id'        => $request->company,
                'phone'             => $request->phone,
                'creci'             => $request->creci,
                'created_by'        => Auth::user()->id,
                'constructor_id'    => $construct
            ]);
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível criar o usuário.')->withInput();
        }

        try {
            $mailer = new Mailer();
            $mailer->sendMailUserCreate($request->email, $request->name, $senha);
        } catch (Exception $e) {
            logging($e);
        }

        try {
            $user_company = UserCompany::create([
                'user_id' => $user->id,
                'company_id' => $request->company,
                'is_coordinator' => in_array($request->role, ['COORDINATOR']) ? 1 : 0
            ]);
        } catch (Exception $e) {
            logging($e);
        }

        if ($request->has('permissions')) $permissions = $request->permissions;

        if ($request->has('projects') && count($request->projects)) {
            foreach ($request->projects as $key => $project) {
                try {
                    $user_project = UserProject::where('user_id', $user->id)->where('project_id', $project)->where('company_id', $request->company)->first();
                    if (!$user_project) {
                        $user_project = UserProject::create([
                            'code'          => getToken(10).time(),
                            'user_id'       => $user->id,
                            'project_id'    => $project,
                            'company_id'    => $request->company
                        ]);

                        try {
                            $proj = Project::find($project);

                            $name = md5(uniqid(rand(), true)).'.pdf';
                            $pdf = PDF::loadView('pdf.user_project_contract', ['project' => $proj, 'user' => $user, 'user_project' => $user_project])->save(storage_path('app/public').'/'.$name);

                            if ($pdf) {
                                $user_project->update([
                                    'file' => $name
                                ]);

                                $mailer = new Mailer();
                                $mailer->sendMailUserProjectContract($user->email, $user->name, $user_project);

                                $user_project->update([
                                    'email_sent' => 1
                                ]);
                            }
                        } catch (Exception $e) {
                            logging($e);
                        }
                    }

                    try {
                        if (isset($permissions[$project]) && count($permissions[$project])) {
                            foreach ($permissions[$project] as $key => $permission) {
                                $user_permission = UserPermission::where('user_project_id', $user_project->id)->where('permission', $permission)->first();
                                if (!$user_permission && in_array($permission, getPermissions($user->role))) {
                                    $user_permission = UserPermission::create([
                                        'user_project_id'   => $user_project->id,
                                        'permission'        => $permission
                                    ]);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        logging($e);
                    }
                } catch (Exception $e) {
                    logging($e);
                }
            }
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function store2(Request $request) {
        if (getRoleIndex($request->role) > getRoleIndex(Auth::user()->role)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $user = User::where('cpf', $request->cpf)->first();

        if ($user) return redirect()->back()->with('error', 'Já existe um usuário cadastrado com esse CPF.');

        try {
            $construct = null;

            if ($request->role == 'INCORPORATOR') {
                if ($request->has('constructor') && Auth::user()->role == 'ADMIN') {
                    $construct = $request->constructor;
                } elseif (Auth::user()->constructor_id != null) {
                    $construct = Auth::user()->constructor_id;
                }
            }

            // $senha = uniqid();
            $senha = onlyNumber($request->cpf);

            $user = User::create([
                'role'              => $request->role,
                'cpf'               => $request->cpf,
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($senha),
                'company_id'        => null,
                'phone'             => $request->phone,
                'creci'             => $request->creci,
                'created_by'        => Auth::user()->id,
                'constructor_id'    => $construct
            ]);
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível criar o usuário.')->withInput();
        }

        try {
            $mailer = new Mailer();
            $mailer->sendMailUserCreate($request->email, $request->name, $senha);
        } catch (Exception $e) {
            logging($e);
        }

        if ($request->has('permissions')) $permissions = $request->permissions;

        if ($request->has('projects') && count($request->projects)) {
            foreach ($request->projects as $key => $project) {
                try {
                    $user_project = UserProject::where('user_id', $user->id)->where('project_id', $project)->where('company_id', $request->company)->first();
                    if (!$user_project) {
                        $user_project = UserProject::create([
                            'code'          => getToken(10).time(),
                            'user_id'       => $user->id,
                            'project_id'    => $project,
                            'company_id'    => 0
                        ]);

                        try {
                            $proj = Project::find($project);

                            $name = md5(uniqid(rand(), true)).'.pdf';
                            $pdf = PDF::loadView('pdf.user_project_contract', ['project' => $proj, 'user' => $user, 'user_project' => $user_project])->save(storage_path('app/public').'/'.$name);

                            if ($pdf) {
                                $user_project->update([
                                    'file' => $name
                                ]);

                                $mailer = new Mailer();
                                $mailer->sendMailUserProjectContract($user->email, $user->name, $user_project);

                                $user_project->update([
                                    'email_sent' => 1
                                ]);
                            }
                        } catch (Exception $e) {
                            logging($e);
                        }
                    }

                    try {
                        if (isset($permissions[$project]) && count($permissions[$project])) {
                            foreach ($permissions[$project] as $key => $permission) {
                                $user_permission = UserPermission::where('user_project_id', $user_project->id)->where('permission', $permission)->first();
                                if (!$user_permission && in_array($permission, getPermissions($user->role))) {
                                    $user_permission = UserPermission::create([
                                        'user_project_id'   => $user_project->id,
                                        'permission'        => $permission
                                    ]);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        logging($e);
                    }
                } catch (Exception $e) {
                    logging($e);
                }
            }
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function attach_admin() {
        return 1;

        $projects = Project::all();
        $admins = User::where('id', '!=', env('SUPERADMIN'))->where('role', 'ADMIN')->get();

        if ($projects->count() && $admins->count()) {
            foreach ($projects as $key => $project) {
                foreach ($admins as $key => $admin) {
                    $user_project = UserProject::where('user_id', $admin->id)->where('project_id', $project->id)->first();

                    if (!$user_project) {
                        //echo "HUE";
                        $user_project = UserProject::create([
                            'code'          => getToken(10).time(),
                            'user_id'       => $admin->id,
                            'project_id'    => $project->id,
                            'company_id'    => 0
                        ]);

                        foreach (getPermissions('ADMIN') as $key => $perm) {
                            $user_permission = UserPermission::create([
                                'user_project_id'   => $user_project->id,
                                'permission'        => $perm
                            ]);
                        }
                    }
                }
            }
        }
    }

    public function user(Request $request) {
        if (!Auth::user()->role == "ADMIN" && !Auth::user()->role == "INCORPORATOR") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $user = User::where('cpf', $request->cpf)->first();

        if ($user) return redirect()->back()->with('error', 'Já existe um usuário cadastrado com esse CPF.');

        if ($request->type != Auth::user()->role) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            // $senha = uniqid();
            $senha = onlyNumber($request->cpf);

            $construct = null;
            if ($request->role == 'INCORPORATOR') $construct = Auth::user()->constructor_id;

            $user = User::create([
                'role'              => $request->type,
                'cpf'               => $request->cpf,
                'name'              => $request->name,
                'email'             => $request->email,
                'password'          => Hash::make($senha),
                'phone'             => $request->phone,
                'created_by'        => Auth::user()->id,
                'constructor_id'    => $construct
            ]);
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível criar o usuário.')->withInput();
        }

        try {
            if ($request->role == 'ADMIN') {
                $projects = Project::all();
                if ($projects->count()) {
                    foreach ($projects as $key => $project) {
                        $user_project = UserProject::where('user_id', $user->id)->where('project_id', $project->id)->first();
                        if (!$user_project) {
                            $user_project = UserProject::create([
                                'code'          => getToken(10).time(),
                                'user_id'       => $user->id,
                                'project_id'    => $project->id,
                                'company_id'    => 0
                            ]);

                            foreach (getPermissions('ADMIN') as $key => $perm) {
                                $user_permission = UserPermission::create([
                                    'user_project_id'   => $user_project->id,
                                    'permission'        => $perm
                                ]);
                            }
                        }
                    }
                }
            }
        } catch (Exception $e) {
            logging($e);
        }

        try {
            $mailer = new Mailer();
            $mailer->sendMailUserCreate($request->email, $request->name, $senha);
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    public function edit(Request $request, $id) {

        if (!$request->has('imobiliaria')) return redirect()->route('home')->with('error', 'Não foi possível completar a operação.');

        $user = User::find($id);
        if(!$user) return redirect()->route('users.create')->with('error', 'Usuário não encontrado.');

        $user_company = UserCompany::where('user_id', $user->id)->where('company_id', $request->imobiliaria)->first();
        if(!$user_company) return redirect()->route('users.create')->with('error', 'Você não tem permissão para realizar essa operação.');

        $this->data['user'] = $user;
        $this->data['permissions'] = Permission::all();

        $imob = $request->imobiliaria;

        $company = Company::find($imob);

        $this->data['projects'] = $company->projects;

        if (Auth::user()->role == 'ADMIN') {
            $this->data['constructors'] = Constructor::all();
        }

        $this->data['company'] = $request->imobiliaria;

        return view('users.edit3', $this->data);
    }

    public function edit_free($id) {

        if(!Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $user = User::find($id);
        if(!$user) return redirect()->route('users.create')->with('error', 'Usuário não encontrado.');

        $this->data['user'] = $user;
        $this->data['permissions'] = Permission::all();

        $this->data['constructors'] = Constructor::all();

        // if(Auth::user()->role == "ADMIN") {
        //     $this->data['projects'] = Project::whereNotIn('id', $user->projects->pluck('id')->toArray())->get();
        // } elseif ($user->role == 'INCORPORATOR' && $user->constructor_id) {
        //     $this->data['projects'] = Project::where('constructor_id', $user->constructor_id)->whereNotIn('id', $user->projects->pluck('id')->toArray())->get();
        //     // $this->data['projects'] = Project::all();
        // }

        if ($user->role == 'INCORPORATOR' && $user->constructor_id) {
            $this->data['projects'] = Project::where('constructor_id', $user->constructor_id)->whereNotIn('id', $user->projects->pluck('id')->toArray())->get();
            // $this->data['projects'] = Project::all();
        }

        $this->data['company'] = 0;

        return view('users.edit_free', $this->data);
    }

    public function update(Request $request, $id) {

        try {
            $user = User::find($id);
            if(!$user) return redirect()->route('home')->with('error', 'Usuário não encontrado.');

            $user_company = UserCompany::where('user_id', $user->id)->where('company_id', $request->company)->first();
            if(!$user_company) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            if(!Auth::user()->companies->contains('id', $request->company)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            $construct = $user->constructor_id;

            if ($user->role != $request->role) {
                if ($user->role == 'INCORPORATOR') {
                    $construct = null;
                }

                if ($request->role == 'INCORPORATOR') {
                    if ($request->has('constructor') && Auth::user()->role == 'ADMIN') {
                        $construct = $request->constructor;
                    } elseif (Auth::user()->constructor_id != null) {
                        $construct = Auth::user()->constructor_id;
                    }
                }

                if ($user->role == 'COORDINATOR') {
                    $user_company = UserCompany::where('user_id', $user->id)->where('company_id', $request->company)->first();
                    if ($user_company) {
                        $user_company->is_coordinator = 0;
                        $user_company->save();
                    }
                }

                if ($request->role == 'COORDINATOR' && $user->companies->contains('is_coordinator', 1)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
            }

            $user->update([
                'role'              => $request->role,
                'name'              => $request->name,
                'company_id'        => $request->company,
                'phone'             => $request->phone,
                'creci'             => $request->creci,
                'constructor_id'    => $construct
            ]);
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível editar o usuário.')->withInput();
        }

        $user_companies = UserCompany::where('user_id', $user->id)->get();
        if ($user->role == 'COORDINATOR') {
            foreach ($user_companies as $user_company) $user_company->update([ 'is_coordinator' => 1 ]);
        } else {
            foreach ($user_companies as $user_company) $user_company->update([ 'is_coordinator' => 0 ]);
        }

        $permissions = array();
        if ($request->has('permissions')) $permissions = $request->permissions;

        $projects = array();
        if ($request->has('projects')) $projects = $request->projects;

        if ($user->projects->count()) {
            foreach ($user->projects as $key => $proj) {
                if (!in_array($proj->id, $projects)) {
                    $user_project = UserProject::where('user_id', $user->id)->where('project_id', $proj->id)->where('company_id', $request->company)->first();
                    if ($user_project) {
                        $user_project->situation = 0;
                        $user_project->save();

                        $user_project->delete();
                    }
                }
            }
        }

        if (count($projects)) {
            foreach ($projects as $key => $project) {
                try {
                    $user_project = UserProject::where('user_id', $user->id)->where('project_id', $project)->where('company_id', $request->company)->first();
                    if (!$user_project) {
                        $user_project = UserProject::create([
                            'code'          => getToken(10).time(),
                            'user_id'       => $user->id,
                            'project_id'    => $project,
                            'company_id'    => $request->company
                        ]);

                        try {
                            $proj = Project::find($project);

                            $name = md5(uniqid(rand(), true)).'.pdf';
                            $pdf = PDF::loadView('pdf.user_project_contract', ['project' => $proj, 'user' => $user, 'user_project' => $user_project])->save(storage_path('app/public').'/'.$name);

                            if ($pdf) {
                                $user_project->update([
                                    'file' => $name
                                ]);

                                $mailer = new Mailer();
                                $mailer->sendMailUserProjectContract($user->email, $user->name, $user_project);

                                $user_project->update([
                                    'email_sent' => 1
                                ]);
                            }
                        } catch (Exception $e) {
                            logging($e);
                        }
                    }

                    try {
                        $user_permissions = UserPermission::where('user_project_id', $user_project->id)->get();
                        if ($user_permissions->count()) {
                            foreach ($user_permissions as $key => $user_p) {
                                if (!isset($permissions[$project]) || (isset($permissions[$project]) && !in_array($user_p->permission, $permissions[$project]))) {
                                    $user_p->delete();
                                }
                            }
                        }

                        if (isset($permissions[$project]) && count($permissions[$project])) {
                            foreach ($permissions[$project] as $key => $permission) {
                                $user_permission = UserPermission::where('user_project_id', $user_project->id)->where('permission', $permission)->first();
                                if (!$user_permission && in_array($permission, getPermissions($user->role))) {
                                    $user_permission = UserPermission::create([
                                        'user_project_id'   => $user_project->id,
                                        'permission'        => $permission
                                    ]);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        logging($e);
                    }
                } catch (Exception $e) {
                    logging($e);
                }
            }
        }

        return redirect()->route('users.index')->with('success', 'Atualizações do usuário salvas com sucesso.');
    }

    public function update_free(Request $request, $id) {
        try {
            $user = User::find($id);
            if(!$user) return redirect()->route('home')->with('error', 'Usuário não encontrado.');

            if(!Auth::user()->role == "ADMIN") return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

            $construct = $user->constructor_id;

            if ($user->role == 'INCORPORATOR') {
                $construct = null;
            }

            if ($request->role == 'INCORPORATOR') {
                if ($request->has('constructor') && Auth::user()->role == 'ADMIN') {
                    $construct = $request->constructor;
                } elseif (Auth::user()->constructor_id != null) {
                    $construct = Auth::user()->constructor_id;
                }
            }

            if ($user->role != $request->role) {

                if ($user->role == 'COORDINATOR') {
                    $user_company = UserCompany::where('user_id', $user->id)->where('company_id', $request->company)->first();
                    if ($user_company) {
                        $user_company->is_coordinator = 0;
                        $user_company->save();
                    }
                }

                if ($request->role == 'COORDINATOR' && $user->companies->contains('is_coordinator', 1)) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');
            }

            $user->update([
                'role'              => $request->role,
                'name'              => $request->name,
                'company_id'        => $request->company,
                'phone'             => $request->phone,
                'creci'             => $request->creci,
                'constructor_id'    => $construct
            ]);
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível editar o usuário.')->withInput();
        }

        $permissions = array();
        if ($request->has('permissions')) $permissions = $request->permissions;

        $projects = array();
        if ($request->has('projects')) $projects = $request->projects;

        if ($user->projects->count()) {
            foreach ($user->projects as $key => $proj) {
                if (!in_array($proj->id, $projects)) {
                    $user_project = UserProject::where('user_id', $user->id)->where('project_id', $proj->id)->where('company_id', $request->company)->first();
                    if ($user_project) {
                        $user_project->situation = 0;
                        $user_project->save();

                        $user_project->delete();
                    }
                }
            }
        }

        if (count($projects)) {
            foreach ($projects as $key => $project) {
                try {
                    $user_project = UserProject::where('user_id', $user->id)->where('project_id', $project)->where('company_id', $request->company)->first();
                    if (!$user_project) {
                        $user_project = UserProject::create([
                            'code'          => getToken(10).time(),
                            'user_id'       => $user->id,
                            'project_id'    => $project,
                            'company_id'    => $request->company
                        ]);

                        try {
                            $proj = Project::find($project);

                            $name = md5(uniqid(rand(), true)).'.pdf';
                            $pdf = PDF::loadView('pdf.user_project_contract', ['project' => $proj, 'user' => $user, 'user_project' => $user_project])->save(storage_path('app/public').'/'.$name);

                            if ($pdf) {
                                $user_project->update([
                                    'file' => $name
                                ]);

                                $mailer = new Mailer();
                                $mailer->sendMailUserProjectContract($user->email, $user->name, $user_project);

                                $user_project->update([
                                    'email_sent' => 1
                                ]);
                            }
                        } catch (Exception $e) {
                            logging($e);
                        }
                    }

                    try {
                        $user_permissions = UserPermission::where('user_project_id', $user_project->id)->get();
                        if ($user_permissions->count()) {
                            foreach ($user_permissions as $key => $user_p) {
                                if (!isset($permissions[$project]) || (isset($permissions[$project]) && !in_array($user_p->permission, $permissions[$project]))) {
                                    $user_p->delete();
                                }
                            }
                        }

                        if (isset($permissions[$project]) && count($permissions[$project])) {
                            foreach ($permissions[$project] as $key => $permission) {
                                $user_permission = UserPermission::where('user_project_id', $user_project->id)->where('permission', $permission)->first();

                                if (!$user_permission && in_array($permission, getPermissions($user->role))) {
                                    $user_permission = UserPermission::create([
                                        'user_project_id'   => $user_project->id,
                                        'permission'        => $permission
                                    ]);
                                }
                            }
                        }
                    } catch (Exception $e) {
                        logging($e);
                    }
                } catch (Exception $e) {
                    logging($e);
                }
            }
        }

        return redirect()->route('users.index')->with('success', 'Atualizações do usuário salvas com sucesso.');
    }

    public function delete(Request $request, $id) {
        return redirect()->back()->with('error', 'Função em desenvolvimento.');

        try {
            $user_attach = UserAttach::where('user_id', Auth::user()->id)->where('attach_id', $id)->where('project_id', $request->empreendimento)->first();

            if(!$user_attach) return redirect()->route('users.index')->with('error', 'Você não tem permissão para realizar essa operação.');

            $user_attach->delete();

            return redirect()->route('users.index')->with('success', 'Usuário desvinculado com sucesso.');
        } catch (Exception $e) {
            logging($e);
            return redirect()->route('users.index')->with('error', 'Não foi possível desvincular o usuário.');
        }
    }

    public function password(Request $request) {
        if(!$request->has('new_password')) return view('users.password', $this->data);
        if(!\Hash::check($request->old_password, \Auth::user()->password)) return redirect()->back()->with('error', 'Senha atual incorreta.');

        try {
            \Auth::user()->update([ 'password' => \Hash::make($request->new_password) ]);
            return redirect()->back()->with('success', 'Senha alterada com sucesso.');
        } catch (\Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível alterar a senha.');
        }
    }

    public function dettach(Request $request, $id) {

        if (!$request->has('imobiliaria')) return redirect()->route('home')->with('error', 'Não foi possível completar a operação.');

        $user = User::find($id);
        if(!$user) return redirect()->route('users.create')->with('error', 'Usuário não encontrado.');

        if (getRoleIndex($user->role) >= getRoleIndex(Auth::user()->role)) return redirect()->back()->with('error', 'Você não tem permissão para realizar essa operação.');

        $user_company = UserCompany::where('user_id', $user->id)->where('company_id', $request->imobiliaria)->first();
        if($user_company) $user_company->delete();

        $user_projects = UserProject::where('user_id', $user->id)->where('company_id', $request->imobiliaria)->get();
        foreach ($user_projects as $user_project) $user_project->delete();

        //$user->update([ 'company_id' => null ]);

        return redirect()->back();
    }
}
