<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Contract;
use App\Proposal;

use Auth;
use Storage;

class ContractController extends Controller
{
    private $data = array();

    public function index() {
        $this->data['projects'] = Auth::user()->projects;
        return view('contracts.index', $this->data);
    }

    public function create() {
        $this->data['projects'] = Auth::user()->projects;
        return view('contracts.create', $this->data);
    }

    public function store(Request $request) {
        try {
            $contract = Contract::create([
                'name'       => $request->name,
                'project_id' => $request->project,
                'content'    => $request->content
            ]);
            return redirect()->route('contracts.index')->with('success', 'Contrato criado com sucesso.');
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível criar o contrato.');
        }
    }

    public function edit($id) {
        $contract = Contract::find($id);
        if(!$contract) return redirect()->back()->with('error', 'Contrato não encontrado.');

        if (!Auth::user()->projects->contains('id', $contract->project_id)) return redirect()->back()->with('error', 'Você não tem permissão para realizar essa operação.');

        $this->data['contract'] = $contract;
        $this->data['projects'] = Auth::user()->projects;

        return view('contracts.edit', $this->data);
    }

    public function update(Request $request, $id) {
        $contract = Contract::find($id);
        if(!$contract) return redirect()->back()->with('error', 'Contrato não encontrado.');

        if (!Auth::user()->projects->contains('id', $contract->project_id)) return redirect()->back()->with('error', 'Você não tem permissão para realizar essa operação.');

        try {
            $contract->update([
                'name'       => $request->name,
                'project_id' => $request->project,
                'content'    => $request->content
            ]);
            return redirect()->route('contracts.index')->with('success', 'Contrato editado com sucesso.');
        } catch (Exception $e) {
            logging($e);
            return redirect()->back()->with('error', 'Não foi possível editar o contrato.');
        }
    }

    public function download($file) {
        return response()->file(storage_path('app/public').'/'.$file);
    }

    public function send(Request $request) {

        if(!$request->has('type')) return 'ERROR';

        if(!$proposal = \App\Proposal::find($request->proposal_id)) return 'ERROR';

        switch ($request->type) {
            case 'EMAIL':
                $retorno = '';

                if((!$request->has('users') || !is_array($request->users) || !count($request->users)) && !strlen($request->emails)) return 'NOT_RECEIVER';

                if($request->has('users') && is_array($request->users) && count($request->users)) {
                    foreach ($request->users as $key => $user) {
                        if(!$proponent = \App\Proponent::where('id', $user)->where('proposal_id', $request->proposal_id)->first()) continue;

                        try {
                            $mailer = new \App\Mailer();
                            $send = $mailer->sendMailProposalContract($proponent->email, $proposal, $proponent);
                            $retorno .= '<div class="text-success">E-Mail para '.$proponent->email.' enviado com sucesso.</div>';
                        } catch (\Exception $e) {
                            $retorno .= '<div class="text-danger">E-Mail para '.$proponent->email.' não foi enviado com sucesso.</div>';
                            logging($e);
                        }
                    }
                }

                if(strlen($request->emails)) {
                    $array_emails = json_decode($request->emails);
                    if(is_array($array_emails)) {
                        $emails = array_column($array_emails, 'value');
                        if(is_array($emails)) {
                            foreach ($emails as $key => $email) {
                                try {
                                    $mailer = new \App\Mailer();
                                    $send = $mailer->sendMailProposalContract($email, $proposal);
                                    $retorno .= '<div class="text-success">E-Mail para '.$email.' enviado com sucesso.</div>';
                                } catch (\Exception $e) {
                                    $retorno .= '<div class="text-danger">E-Mail para '.$email.' não foi enviado com sucesso.</div>';
                                    logging($e);
                                }
                            }
                        }
                    }
                }

                return $retorno;
            break;
            case 'PHONE':
                $retorno = '';

                if((!$request->has('users') || !is_array($request->users) || !count($request->users)) && !strlen($request->phones)) return 'NOT_RECEIVER';

                if($request->has('users') && is_array($request->users) && count($request->users)) {
                    foreach ($request->users as $key => $user) {
                        if(!$proponent = \App\Proponent::where('id', $user)->where('proposal_id', $request->proposal_id)->first()) continue;

                        try {
                            $mailer = new \App\Mailer();
                            $send = $mailer->sendMailProposalContractWP($proponent->cellphone, $proposal, $proponent);
                            $retorno .= '<div class="text-success">Enviar para o número '.$proponent->cellphone.', clique <a href="'.$send.'" target="_BLANK">aqui</a></div>';
                        } catch (\Exception $e) {
                            $retorno .= '<div class="text-danger">Link para o número '.$proponent->cellphone.' não foi gerado com sucesso.</div>';
                            logging($e);
                        }
                    }
                }

                if(strlen($request->phones)) {
                    $array_phones = json_decode($request->phones);
                    if(is_array($array_phones)) {
                        $phones = array_column($array_phones, 'value');
                        if(is_array($phones)) {
                            foreach ($phones as $key => $phone) {
                                try {
                                    $mailer = new \App\Mailer();
                                    $send = $mailer->sendMailProposalContractWP($phone, $proposal);
                                    $retorno .= '<div class="text-success">Enviar para o número '.$phone.', clique <a href="'.$send.'" target="_BLANK">aqui</a></div>';
                                } catch (\Exception $e) {
                                    $retorno .= '<div class="text-danger">Link para o número '.$phone.' não foi gerado com sucesso.</div>';
                                    logging($e);
                                }
                            }
                        }
                    }
                }
                
                return $retorno;
            break;
            default:
                return 'ERROR';
            break;
        }

        // return 'SUCCESS';
    }
}
