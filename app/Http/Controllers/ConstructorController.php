<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Constructor;
use App\User;
use App\Mailer;

use Auth;
use Exception;
use Hash;

class ConstructorController extends Controller
{
    private $data = array();

    public function index() {
        $this->data['constructors'] = Constructor::all();
        return view('constructors.index', $this->data);
    }

    public function create() {
        return view('constructors.create', $this->data);
    }

    public function store(Request $request) {
        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            $constructor = Constructor::create([
                'name' => $request->name,
                'cnpj' => $request->cnpj,
            ]);
        } catch(Exception $e) {
            logging($e);
            return redirect()->route('constructors.index')->with('error', 'Não foi possível cadastrar a incorporadora.');
        }

        $user = null;

        try {
            $has = User::where('cpf', $request->cpf)->first();
            if (!$has) {
                $senha = uniqid();

                $user = User::create([
                    'role'              => 'INCORPORATOR',
                    'cpf'               => $request->cpf,
                    'name'              => $request->manager,
                    'email'             => $request->email,
                    'password'          => Hash::make($senha),
                    'phone'             => $request->cellphone,
                    'created_by'        => Auth::user()->id,
                    'constructor_id'    => $constructor->id
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
        }

        return redirect()->route('constructors.index')->with('success', 'Incorporadora cadatrada com sucesso.');
    }

    public function edit($id) {
        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $constructor = Constructor::find($id);
        if(!$constructor) return redirect()->route('constructors.index')->with('error', 'Não foi possível completar a operação.');

        $this->data['constructor'] = $constructor;

        return view('constructors.edit', $this->data);
    }

    public function update(Request $request, $id) {
        if(getRoleIndex(Auth::user()->role) < 2) return redirect()->route('home')->with('error', 'Você não tem permissão para realizar essa operação.');

        $constructor = Constructor::find($id);
        if(!$constructor) return redirect()->route('constructors.index')->with('error', 'Não foi possível completar a operação.');

        try {
            $constructor->update([
                'name' => $request->name,
                'cnpj' => $request->cnpj
            ]);

            foreach ($constructor->users as $key => $user) {
                $user->update([ 'receive_emails' => 0 ]);
            }

            if($request->has('users') && is_array($request->users) && count($request->users)) {
                foreach ($request->users as $key => $user) {
                    $u = \App\User::find($user);
                    if($u) $u->update([ 'receive_emails' => 1 ]);
                }
            }

            return redirect()->route('constructors.index')->with('success', 'Incorporadora editada com sucesso.');
        } catch(Exception $e) {
            logging($e);
            return redirect()->route('constructors.index')->with('error', 'Não foi possível editar a incorporadora.');
        }
    }

    public function delete($id) {}
}
