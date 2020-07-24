<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CompanyProject;
use App\UserProject;
use App\Property;
use App\Block;
use App\Building;
use App\Project;

class IndexController extends Controller
{
    private $data = array();

    public function index() {
        return view('index', $this->data);
    }

    public function contact(Request $request) {

        $captcha_data = null;
        if (isset($_POST['g-recaptcha-response'])) $captcha_data = $_POST['g-recaptcha-response'];
        if (!$captcha_data) return redirect()->back()->with('contact_error', 'Selecione a checkbox para validar o formulário.')->withInput();

        $resposta = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=6LeRvr0UAAAAAOTW9dfIg7sGkIpcB6F6KoKWpWWw&response=".$captcha_data."&remoteip=".$_SERVER['REMOTE_ADDR']);
        $resposta = json_decode($resposta);

        if (!isset($resposta->success) || !$resposta->success) return redirect()->route('message')->with('contact_error', 'não foi possível enviar a mensagem :(')->withInput();

        try {
            $mailer = new \App\Mailer();
            $mailer->sendMailContact($request->all());
        } catch (\Exception $e) {
            logging($e);
            // return 0;
            return redirect()->back()->with('contact_error', 'não foi possível enviar a mensagem :(')->withInput();
        }

        // return 1;
        return redirect()->back()->with('contact_success', 'obrigado pela sua mensagem.');
    }

    public function validation(Request $request) {
        if ($request->has('code')) {
            $this->data['code'] = $request->code;
            $user_project = UserProject::where('code', $request->code)->withTrashed()->first();
            if ($user_project) {
                $this->data['user'] = true;
                $this->data['contract'] = $user_project;
            } else {
                $company_project = CompanyProject::where('code', $request->code)->withTrashed()->first();
                if ($company_project) {
                    $this->data['company'] = true;
                    $this->data['contract'] = $company_project;
                } else {
                    $this->data['not_found'] = true;
                }
            }
        }

        $this->data['hide'] = true;

        return view('validation', $this->data);
    }

    public function simulator(Request $request) {
        if ($request->has('lote')) {
            $this->data['lote'] = $request->lote;
            $this->data['parcelas'] = $request->parcelas;
            $this->data['entrada'] = $request->entrada;

            $property = Property::find($request->lote);

            $taxa = $property->block->building->project->fee/100;

            $parcelas = $request->parcelas;

            $entrada = toCoin($request->entrada);

            if ($entrada >= $property->value) {
                $this->data['up'] = true;
            } else {
                $minimo = $property->value*$property->block->building->project->minimium_percentage;

                if ($entrada < $minimo) {
                    $this->data['down'] = true;
                } else {
                    $total = $property->value - $entrada;
                    $porcentagem = 1 + $taxa;

                    $potencia = pow($porcentagem, $parcelas);

                    $cima = pow($porcentagem, $parcelas) * $taxa;
                    $baixo = pow($porcentagem, $parcelas) - 1;

                    $prestacao = $total * ($cima / $baixo);

                    $this->data['ok'] = 'R$ '.formatMoney($prestacao);
                }
            }
        }

        if(!$request->has('empreendimento')) redirect()->back()->with('error', 'Emprendimento não encontrado.');

        $project = Project::where('id', $request->empreendimento)->where('simulator', 1)->first();
        if ($project) {
            $this->data['properties'] = $project->properties;
            $this->data['empreendimento'] = $request->empreendimento;

            $this->data['hide'] = true;

            return view('simulator', $this->data);
        }

        return redirect()->back()->with('error', 'Emprendimento não encontrado.');
    }

    public function map($slug) {
        if(!$project = Project::where('url', $slug)->first()) return redirect()->route('home');

        $this->data['project'] = $project;
        $this->data['hide'] = true;
        $this->data['is_map'] = true;

        $this->data['colors'] = \App\Color::all()->mapWithKeys(function ($item) {
            return [ $item->status => $item->color ];
        })->toArray();

        return view('map', $this->data);
    }

    public function error(){
        return response()->view('error');
    }
}
