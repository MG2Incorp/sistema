<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Project;
use App\Building;
use App\Block;
use App\ProjectDocument;
use App\Constructor;
use App\Company;
use App\CompanyProject;
use App\Mailer;
use App\Lead;
use App\UserProject;
use App\UserPermission;
use App\User;
use App\UserCompany;

use Auth;
use Exception;
use Storage;
use Log;
use PDF;
use Excel;

use Gregwar\Image\Image;

class ProjectController extends Controller
{
    private $data = array();

    public function index(Request $request) {
        // if(Auth::user()->role == 'ADMIN') {
        //     $this->data['projects'] = Project::all();
        // } else {
        //     $this->data['projects'] = Auth::user()->projects;
        // }

        if ($request->has('export')) {

            $project = Project::find($request->project_id);
            if(!$project) return redirect()->back()->with('error', 'Empreendimento não encontrado.');

            if (!Auth::user()->projects->contains('id', $project->id)) return redirect()->back()->with('error', 'Você não tem permissão para completar a operação.');

            Excel::create('Leads_'.studly_case($project->name), function($excel) use ($project) {
                $excel->sheet('Pagamentos', function($sheet) use ($project) {
                    $sheet->row(1, [
                        'NOME',
                        'DATA',
                        'E-MAIL',
                        'CELULAR']
                    );

                    $count = 2;
                    foreach ($project->leads as $key => $lead) {
                        $sheet->row($count, [
                            $lead->name,
                            dateString($lead->created_at),
                            $lead->email,
                            $lead->cellphone]
                        );
                        $count++;
                    }
                });
            })->download('xlsx');
        }

        $this->data['projects'] = Project::all();

        return view('projects.index', $this->data);
    }

    public function create() {
        $this->data['constructors'] = Constructor::all();
        $this->data['companies'] = Company::all();
        $this->data['owners'] = \App\Owner::where('status', 'ACTIVE')->get();
        $this->data['indexes'] = \App\MonetaryCorrectionIndex::all();

        return view('projects.create2', $this->data);
    }

    public function store(Request $request) {
        $foto = null;
        if ($request->hasFile('file')) {
            $file = $request->file;
            $ext = $file->getClientOriginalExtension();
            $formats = ['png', 'gif', 'jpg', 'jpeg'];
            if(in_array($ext, $formats)) {
                $tmp_name = $file->getPathName();
                $name = md5(uniqid(rand(), true));

                $filename = sprintf(env('PROJECTS_IMAGES_DIR').'%s.%s', $name, $ext);
                $save = Image::open($tmp_name)->save($filename, $ext);

                $foto = $name.".".$ext;
            }
        }

        $foto2 = null;
        if ($request->hasFile('file2')) {
            $file = $request->file2;
            $ext = $file->getClientOriginalExtension();
            $formats = ['png', 'gif', 'jpg', 'jpeg'];
            if(in_array($ext, $formats)) {
                $tmp_name = $file->getPathName();
                $name = md5(uniqid(rand(), true));

                $filename = sprintf(env('PROJECTS_IMAGES_DIR').'%s.%s', $name, $ext);
                $save = Image::open($tmp_name)->save($filename, $ext);

                $foto2 = $name.".".$ext;
            }
        }

        $bg = null;
        if ($request->hasFile('bg')) {
            $file = $request->bg;
            $ext = $file->getClientOriginalExtension();
            $formats = ['png', 'gif', 'jpg', 'jpeg'];
            if(in_array($ext, $formats)) {
                $tmp_name = $file->getPathName();
                $name = md5(uniqid(rand(), true));

                $filename = sprintf(env('PROJECTS_IMAGES_DIR').'%s.%s', $name, $ext);
                $save = Image::open($tmp_name)->save($filename, $ext);

                $bg = $name.".".$ext;
            }
        }

        $indexes = null;
        if ($request->has('indexes') && count($request->indexes)) $indexes = implode(',', $request->indexes);

        $url = str_slug($request->name);
        $p = Project::where('url', $url)->first();
        $count = 2;
        $aux = $url;
        while ($p) {
            $url = $aux.'-'.$count;
            $count++;
            $p = Project::where('url', $url)->first();
        }

        try {
            $project = Project::create([
                'social_name'           => $request->social_name,
                'cnpj'                  => $request->cnpj,
                'name'                  => $request->name,
                // 'finish_at'             => dataToSQL($request->finish),
                'finish_at'             => $request->finish,
                'status'                => $request->status,
                'local'                 => $request->local,
                'photo'                 => $foto,
                'type'                  => $request->type,
                'constructor_id'        => $request->constructor,
                'notes'                 => $request->notes,
                'expiration_time'       => $request->time,
                'comission'             => toCoin($request->comission),
                'fee'                   => toCoin($request->fee),
                'minimum_percentage'    => toCoin($request->minimum_percentage),
                'simulator'             => $request->simulator,
                'indexes'               => $indexes,
                'background_image'      => $bg,
                'chat'                  => $request->chat,
                'url'                   => $url,
                'photo2'                => $foto2,
                'chat_code'             => $request->chat_code
            ]);
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível salvar o empreendimento.')->withInput();
        }

        $user_project = UserProject::where('user_id', env('SUPERADMIN'))->where('project_id', $project->id)->first();
        if (!$user_project) {
            $user_project = UserProject::create([
                'code'          => getToken(10).time(),
                'user_id'       => env('SUPERADMIN'),
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

        try {
            $admins = User::where('id', '!=', env('SUPERADMIN'))->where('role', 'ADMIN')->get();
            if ($admins->count()) {
                foreach ($admins as $key => $admin) {
                    $user_project = UserProject::where('user_id', $admin->id)->where('project_id', $project->id)->first();
                    if (!$user_project) {
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
        } catch (Exception $e) {
            logging($e);
        }

        if ($request->has('buildings')) {
            try {
                $buildings = $request->buildings;
                foreach ($buildings as $key => $building) {
                    $b = Building::create([
                        'project_id' => $project->id,
                        'name'       => $building
                    ]);
                }
            } catch (Exception $e) {
                logging($e);
            }
        }

        $incorps = User::where('constructor_id', $request->constructor)->get();

        if ($request->has('companies')) {
            try {
                $companies = $request->companies;
                foreach ($companies as $key => $company) {
                    $b = CompanyProject::create([
                        'code' => getToken(10).time(),
                        'company_id' => $company,
                        'project_id' => $project->id
                    ]);

                    $comp = Company::find($company);

                    if ($comp->coordinators->count()) {
                        foreach ($comp->coordinators as $key => $coord) {
                            $user_project = UserProject::where('user_id', $coord->id)->where('project_id', $project->id)->first();
                            if (!$user_project) {
                                $user_project = UserProject::create([
                                    'code'       => getToken(10).time(),
                                    'user_id'    => $coord->id,
                                    'project_id' => $project->id,
                                    'company_id' => $comp->id
                                ]);
                            }
                        }
                    }

                    if ($incorps->count()) {
                        foreach ($incorps as $key => $incorp) {
                            // $user_company = UserCompany::where('user_id', $incorp->id)->where('company_id', $comp->id)->first();
                            // if (!$user_company) {
                            //     $user_company = UserCompany::create([
                            //         'user_id' => $incorp->id,
                            //         'company_id' => $comp->id,
                            //         'is_coordinator' => 0
                            //     ]);
                            // }

                            $user_project = UserProject::where('user_id', $incorp->id)->where('project_id', $project->id)->first();
                            if (!$user_project) {
                                $user_project = UserProject::create([
                                    'code'       => getToken(10).time(),
                                    'user_id'    => $incorp->id,
                                    'project_id' => $project->id,
                                    'company_id' => 0
                                ]);
                            }
                        }
                    }

                    try {
                        $comp = Company::find($company);

                        $name = md5(uniqid(rand(), true)).'.pdf';
                        $pdf = PDF::loadView('pdf.company_project_contract', ['project' => $project, 'company' => $comp, 'company_project' => $b])->save(storage_path('app/public').'/'.$name);

                        if ($pdf) {
                            $b->update([
                                'file' => $name
                            ]);

                            // $mailer = new Mailer();
                            // $mailer->sendMailCompanyProjectContract($comp->email, $comp->name, $b);

                            // $b->update([
                            //     'email_sent' => 1
                            // ]);
                        }
                    } catch (Exception $e) {
                        logging($e);
                    }
                }
            } catch (Exception $e) {
                logging($e);
            }
        }

        try {
            if($request->has('select_owner') && is_array($request->select_owner) && count($request->select_owner)) {
                foreach ($request->select_owner as $key => $select_owner) {
                    if(is_array($select_owner) && count($select_owner)) {
                        foreach ($select_owner as $so) {
                            \App\ProjectOwner::firstOrCreate([
                                'project_id'    => $project->id,
                                'owner_id'      => $key,
                                'account_id'    => $so
                            ]);
                        }
                    }
                }
            }

            if($request->has('select_owners') && is_array($request->select_owners) && count($request->select_owners)) {
                foreach ($request->select_owners as $key => $select_owners) {
                    \App\ProjectOwner::firstOrCreate([
                        'project_id'    => $project->id,
                        'owner_id'      => $key
                    ]);
                }
            }
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->route('projects.index')->with('success', 'Empreendimento criado com sucesso.');
    }

    public function map(Request $request) {
        if(!$project = Project::find($request->project_id)) return redirect()->back()->with('error', 'Empreendimento não encontrado.');

        $project->update([ 'fields' => $request->fields ? json_encode($request->fields) : null ]);

        if ($request->hasFile('map')) {
            $file = $request->map;
            $ext = $file->getClientOriginalExtension();
            $formats = ['png', 'gif', 'jpg', 'jpeg'];
            if(in_array($ext, $formats)) {
                $tmp_name = $file->getPathName();
                $name = md5(uniqid(rand(), true));

                $filename = sprintf(env('PROJECTS_IMAGES_DIR').'%s.%s', $name, $ext);
                $save = Image::open($tmp_name)->save($filename, $ext);

                $project->update([ 'map' => $name.".".$ext ]);
            }
        }

        if($request->has('properties') && is_array($request->properties) && count($request->properties)) {
            foreach ($request->properties as $key => $property) {
                //if(isset($request->shapes[$property]) && $request->shapes[$property]) {
                    \App\Map::updateOrCreate(
                        [ 'project_id' => $request->project_id, 'property_id' => $property ],
                        [
                            'coordinates' => isset($request->coords[$property]) ? $request->coords[$property] : null,
                            'shape' => isset($request->shapes[$property]) ? $request->shapes[$property] : null
                        ]
                    );
                //}
            }
        }

        return redirect()->back();
    }

    public function owner(Request $request) {
        if(!$project = Project::find($request->project_id)) return redirect()->back()->with('error', 'Empreendimento não encontrado.');

        $attachs = [];
        try {
            if($request->has('select_owner') && is_array($request->select_owner) && count($request->select_owner)) {
                foreach ($request->select_owner as $key => $select_owner) {
                    if(is_array($select_owner) && count($select_owner)) {
                        foreach ($select_owner as $so) {
                            $attachs[] = $so;
                            \App\ProjectOwner::firstOrCreate([
                                'project_id'    => $project->id,
                                'owner_id'      => $key,
                                'account_id'    => $so
                            ]);
                        }
                    }
                }
            }

            if($request->has('select_owners') && is_array($request->select_owners) && count($request->select_owners)) {
                foreach ($request->select_owners as $key => $select_owners) {
                    \App\ProjectOwner::firstOrCreate([
                        'project_id'    => $project->id,
                        'owner_id'      => $key
                    ]);
                }
            }

            if($project->accounts->count()) {
                foreach($project->accounts as $account) {
                    if(in_array($account->account_id, $attachs)) continue;
                    if($project->properties->where('account_id', $account->account_id)->count()) continue;

                    $account->delete();
                }
            }
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->back();
    }

    public function billing(Request $request) {
        if(!$project = \App\Project::find($request->project_id)) return redirect()->back()->with('error', 'Empreendimento não encontrado.');
        if(!$account = \App\Account::find($request->account_id)) return redirect()->back()->with('error', 'Conta não encontrada.');
        if(!$project_owner = \App\ProjectOwner::where('project_id', $request->project_id)->where('account_id', $request->account_id)->first()) return redirect()->back()->with('error', 'Não foi possível completar a operação.');

        $project_owner->update([
            'TituloDocEspecie'          => $request->TituloDocEspecie,
            'TituloDataDesconto'        => $request->TituloDataDesconto,
            'TituloCodDesconto'         => $request->TituloCodDesconto,
            'TituloValorDescontoTaxa'   => toCoin($request->TituloValorDescontoTaxa),
            'TituloDataJuros'           => $request->TituloDataJuros,
            'TituloCodigoJuros'         => $request->TituloCodigoJuros,
            'TituloValorJuros'          => toCoin($request->TituloValorJuros),
            'TituloDataMulta'           => $request->TituloDataMulta,
            'TituloCodigoMulta'         => $request->TituloCodigoMulta,
            'TituloValorMultaTaxa'      => toCoin($request->TituloValorMultaTaxa),
            'TituloCodProtesto'         => $request->TituloCodProtesto,
            'TituloPrazoProtesto'       => $request->TituloPrazoProtesto,
            'TituloCodBaixaDevolucao'   => $request->TituloCodBaixaDevolucao,
            'TituloPrazoBaixa'          => $request->TituloPrazoBaixa,
            'TituloAceite'              => $request->TituloAceite,
            'TituloLocalPagamento'      => $request->TituloLocalPagamento,
            'TituloCodEmissaoBloqueto'  => $request->TituloCodEmissaoBloqueto
        ]);

        return redirect()->back();
    }

    public function billing_method(Request $request) {
        if(!$project = Project::find($request->project_id)) return redirect()->back()->with('error', 'Empreendimento não encontrado.');

        try {
            $project->update([ 'send_billets' => $request->send_billets ]);
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->back();
    }

    public function edit($id) {
        $project = Project::find($id);
        if (!$project) return redirect()->back()->with('error', 'Empreendimento não encontrado.');

        $this->data['constructors'] = Constructor::all();
        $this->data['companies'] = Company::all();
        $this->data['owners'] = \App\Owner::where('status', 'ACTIVE')->get();
        $this->data['indexes'] = \App\MonetaryCorrectionIndex::all();

        $this->data['project'] = $project;

        return view('projects.edit2', $this->data);
    }

    public function update(Request $request, $id) {
        try {
            $project = Project::find($id);

            $foto = $project->photo;
            if ($request->hasFile('file')) {
                $file = $request->file;
                $ext = $file->getClientOriginalExtension();
                $formats = ['png', 'gif', 'jpg', 'jpeg'];
                if(in_array($ext, $formats)) {
                    $tmp_name = $file->getPathName();
                    $name = md5(uniqid(rand(), true));

                    $filename = sprintf(env('PROJECTS_IMAGES_DIR').'%s.%s', $name, $ext);
                    $save = Image::open($tmp_name)->save($filename, $ext);

                    $foto = $name.".".$ext;
                }
            }

            $foto2 = $project->photo2;
            if ($request->hasFile('file2')) {
                $file = $request->file2;
                $ext = $file->getClientOriginalExtension();
                $formats = ['png', 'gif', 'jpg', 'jpeg'];
                if(in_array($ext, $formats)) {
                    $tmp_name = $file->getPathName();
                    $name = md5(uniqid(rand(), true));

                    $filename = sprintf(env('PROJECTS_IMAGES_DIR').'%s.%s', $name, $ext);
                    $save = Image::open($tmp_name)->save($filename, $ext);

                    $foto2 = $name.".".$ext;
                }
            }

            $bg = $project->background_image;
            if ($request->hasFile('bg')) {
                $file = $request->bg;
                $ext = $file->getClientOriginalExtension();
                $formats = ['png', 'gif', 'jpg', 'jpeg'];
                if(in_array($ext, $formats)) {
                    $tmp_name = $file->getPathName();
                    $name = md5(uniqid(rand(), true));

                    $filename = sprintf(env('PROJECTS_IMAGES_DIR').'%s.%s', $name, $ext);
                    $save = Image::open($tmp_name)->save($filename, $ext);

                    $bg = $name.".".$ext;
                }
            }

            $indexes = null;
            if ($request->has('indexes') && count($request->indexes)) $indexes = implode(',', $request->indexes);

            $url = str_slug($request->name);
            if ($url != $project->url) {
                $p = Project::where('url', $url)->first();
                $count = 2;
                $aux = $url;
                while ($p) {
                    $url = $aux.'-'.$count;
                    $count++;
                    $p = Project::where('url', $url)->first();
                }
            }

            $project->update([
                'social_name'           => $request->social_name,
                'cnpj'                  => $request->cnpj,
                'name'                  => $request->name,
                // 'finish_at'             => dataToSQL($request->finish),
                'finish_at'             => $request->finish,
                'status'                => $request->status,
                'local'                 => $request->local,
                'photo'                 => $foto,
                'type'                  => $request->type,
                /*'constructor_id'        => $request->constructor,*/
                'notes'                 => $request->notes,
                'expiration_time'       => $request->time,
                'comission'             => toCoin($request->comission),
                'fee'                   => toCoin($request->fee),
                'minimum_percentage'    => toCoin($request->minimum_percentage),
                'simulator'             => $request->simulator,
                'indexes'               => $indexes,
                'background_image'      => $bg,
                'chat'                  => $request->chat,
                'url'                   => $url,
                'photo2'                => $foto2,
                'chat_code'             => $request->chat_code
            ]);

            if ($request->has('old_buildings')) {
                try {
                    $buildings = $request->old_buildings;
                    foreach ($buildings as $key => $building) {
                        $b = Building::where('id', $key)->where('project_id', $project->id)->first();
                        if ($b) {
                            $b->update([
                                'name' => $building
                            ]);
                        }
                    }
                } catch (Exception $e) {
                    logging($e);
                }
            }

            if ($request->has('buildings')) {
                try {
                    $buildings = $request->buildings;
                    foreach ($buildings as $key => $building) {
                        $b = Building::create([
                            'project_id' => $project->id,
                            'name'       => $building
                        ]);
                    }
                } catch (Exception $e) {
                    logging($e);
                }
            }

            $incorps = User::where('constructor_id', $project->constructor_id)->get();

            if ($request->has('companies')) {
                if ($project->companies->count()) {
                    foreach ($project->companies as $key => $company) {
                        if (!in_array($company->id, $request->companies)) {
                            $comp_p = CompanyProject::where('project_id', $project->id)->where('company_id', $company->id)->first();
                            if ($comp_p) {

                                $c_id = $comp_p->company_id;

                                $users_projects = UserProject::where('project_id', $project->id)->where('company_id', $c_id)->get();
                                if ($users_projects->count()) {
                                    foreach ($users_projects as $key => $user_p) {
                                        $user_p->delete();
                                    }
                                }

                                $comp_p->delete();
                            }
                        }
                    }
                }

                try {
                    $companies = $request->companies;
                    foreach ($companies as $key => $company) {
                        $comp_p = CompanyProject::where('project_id', $project->id)->where('company_id', $company)->first();
                        if (!$comp_p) {
                            $b = CompanyProject::create([
                                'code' => getToken(10).time(),
                                'company_id' => $company,
                                'project_id' => $project->id
                            ]);

                            try {
                                $comp = Company::find($company);

                                $name = md5(uniqid(rand(), true)).'.pdf';
                                $pdf = PDF::loadView('pdf.company_project_contract', ['project' => $project, 'company' => $comp, 'company_project' => $b])->save(storage_path('app/public').'/'.$name);

                                if ($pdf) {
                                    $b->update([
                                        'file' => $name
                                    ]);

                                    // $mailer = new Mailer();
                                    // $mailer->sendMailCompanyProjectContract($comp->email, $comp->name, $b);

                                    // $b->update([
                                    //     'email_sent' => 1
                                    // ]);
                                }
                            } catch (Exception $e) {
                                logging($e);
                            }
                        }


                        $comp = Company::find($company);

                        if ($comp->coordinators->count()) {
                            foreach ($comp->coordinators as $key => $coord) {
                                $user_project = UserProject::where('user_id', $coord->id)->where('project_id', $project->id)->first();
                                if (!$user_project) {
                                    $user_project = UserProject::create([
                                        'code'       => getToken(10).time(),
                                        'user_id'    => $coord->id,
                                        'project_id' => $project->id,
                                        'company_id' => $comp->id
                                    ]);
                                }
                            }
                        }

                        if ($incorps->count()) {
                            foreach ($incorps as $key => $incorp) {
                                // $user_company = UserCompany::where('user_id', $incorp->id)->where('company_id', $comp->id)->first();
                                // if (!$user_company) {
                                //     $user_company = UserCompany::create([
                                //         'user_id' => $incorp->id,
                                //         'company_id' => $comp->id,
                                //         'is_coordinator' => 0
                                //     ]);
                                // }

                                $user_project = UserProject::where('user_id', $incorp->id)->where('project_id', $project->id)->first();
                                if (!$user_project) {
                                    $user_project = UserProject::create([
                                        'code'       => getToken(10).time(),
                                        'user_id'    => $incorp->id,
                                        'project_id' => $project->id,
                                        'company_id' => 0
                                    ]);
                                }
                            }
                        }
                    }
                } catch (Exception $e) {
                    logging($e);
                }
            } else {
                if ($project->companies->count()) {
                    $comp_p = CompanyProject::where('project_id', $project->id)->get();
                    foreach ($comp_p as $key => $cp) {

                        $c_id = $cp->company_id;

                        $users_projects = UserProject::where('project_id', $project->id)->where('company_id', $c_id)->get();
                        if ($users_projects->count()) {
                            foreach ($users_projects as $key => $user_p) {
                                $user_p->delete();
                            }
                        }

                        $cp->delete();
                    }
                }
            }
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível atualizar o empreendimento.');
        }

        return redirect()->route('projects.index')->with('success', 'Empreendimento atualizado com sucesso.');
    }

    public function delete($id) {
        try {
            $project = Project::find($id);

            $user_projects = UserProject::where('project_id', $id)->get();
            if ($user_projects->count()) {
                foreach ($user_projects as $user_p) {
                    $permissions = UserPermission::where('user_project_id', $user_p->id)->get();
                    if ($permissions->count()) {
                        foreach ($permissions as $key => $permission) {
                            $permission->delete();
                        }
                    }
                }
            }

            $project->delete();

            return redirect()->route('projects.index')->with('success', 'Empreendimento deletado com sucesso.');
        } catch(Exception $e) {
            logging($e);
            return redirect()->route('projects.index')->with('error', 'Não foi possível deletar o empreendimento.');
        }
    }

    public function buildings(Request $request) {
        $buildings = Building::where('project_id', $request->id)->get();

        $h = '';

        if ($buildings->count()) {
            $h .= '<option value="">Selecione...</option>';
            foreach ($buildings->sortBy('name') as $b) {
                $h .= '<option value="'.$b->id.'">'.$b->name.'</option>';
            }
        }

        return $h;
    }

    public function blocks(Request $request) {
        $blocks = Block::where('building_id', $request->id)->get();

        $h = '';

        if ($blocks->count()) {
            $h .= '<option value="">Selecione...</option>';
            foreach ($blocks->sortBy('label') as $b) {
                $h .= '<option value="'.$b->id.'">'.$b->label.'</option>';
            }
        }

        return $h;
    }

    public function document(Request $request) {
        if(!in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) return redirect()->back()->with('error', 'Você não tem permissão necessária para completar essa operação.');

        $arq = null;
        if ($request->hasFile('file')) {
            $file = $request->file;
            $ext = $file->getClientOriginalExtension();

            $formats = ['gif', 'bmp', 'png', 'jpg', 'jpeg', 'pdf', 'rar', 'zip', 'html', 'txt', 'tar', 'docx'];

            if(in_array($ext, $formats)) {
                $tmp_name = $file->getPathName();
                $name = md5(uniqid(rand(), true));

                $filename = sprintf(env('DOCUMENTS_DIR').'%s.%s', $name, $ext);

                $t = Storage::put($filename, file_get_contents($tmp_name));

                if ($t) $arq = $name.".".$ext;
            }
        }

        if (!$arq) return redirect()->back()->with('error', 'Não foi possível salvar o documento');

        try {
            $document = ProjectDocument::create([
                'user_id'       => Auth::user()->id,
                'project_id'    => $request->project_id,
                'file'          => $arq,
                'description'   => $request->description
            ]);

            return redirect()->back()->with('success', 'Documento salvo com sucesso');
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível salvar o documento.');
        }
    }

    public function download($file) {
        return Storage::download(env('DOCUMENTS_DIR').$file);
    }

    public function lead($slug) {
        $project = Project::where('url', $slug)->first();
        if (!$project) return redirect()->route('home');

        $this->data['project'] = $project;

        $this->data['hide'] = true;

        return view('lead', $this->data);
    }

    public function lead_store(Request $request) {
        try {
            $lead = Lead::create([
                'project_id' => $request->project_id,
                'name'       => $request->name,
                'email'      => $request->email,
                'cellphone'  => $request->cellphone
            ]);
            return redirect()->back()->with('success', 'Informações salvas com sucesso.');
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível salvar as informações.')->withInput();
        }
    }

    public function deleteDocument($id) {
        if(!in_array(Auth::user()->role, ['ADMIN', 'INCORPORATOR'])) return redirect()->back()->with('error', 'Você não tem permissão necessária para completar essa operação.');

        try {
            $document = ProjectDocument::find($id);
            if ($document) {
                $document->delete();
                return redirect()->back()->with('success', 'Documento deletado com sucesso');
            }
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->back()->with('error', 'Não foi possível deletar o documento.');
    }

    public function companyContractSend(Request $request) {

        try {
            $company_project = CompanyProject::find($request->contract);
            if ($company_project) {
                $mailer = new Mailer();
                $mailer->sendMailCompanyProjectContract($company_project->company->email, $company_project->company->name, $company_project);

                return redirect()->back()->with('success', 'E-Mail enviado com sucesso.');
            }
        } catch (Exception $e) {
            logging($e);
        }

        return redirect()->back()->with('error', 'Não foi possível enviar o e-mail.');
    }
}
