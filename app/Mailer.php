<?php

namespace App;

use App\Mail\ContractAvaliableMail;
use App\Mail\UserProjectMail;
use App\Mail\CompanyProjectContractMail;
use App\Mail\UserProjectContractMail;
use App\Mail\UserCreateMail;
use App\Mail\ProposalContractMail;
use App\Mail\ContactMail;
use App\Mail\OutdatedBillingMail;
use App\Mail\ProposalStatusMail;

use Mail;

class Mailer {

    public function __contruct() {}

    public function sendMailProposalContractAvailable($id, $email, $nome = null) {
        Mail::to($email, $nome)->bcc('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new ContractAvaliableMail($id));
    }

    public function sendMailUserProject($email, $nome = null, $proj) {
        Mail::to($email, $nome)->bcc('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new UserProjectMail($proj));
    }

    public function sendMailCompanyProjectContract($email, $nome = null, $contract) {
        Mail::to($email, $nome)->bcc('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new CompanyProjectContractMail($contract));
    }

    public function sendMailUserProjectContract($email, $nome = null, $contract) {
        Mail::to($email, $nome)->bcc('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new UserProjectContractMail($contract));
    }

    public function sendMailUserCreate($email, $nome = null, $pass) {
        Mail::to($email, $nome)->bcc('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new UserCreateMail($email, $pass));
    }

    public function sendMailProposalContract($email, $proposal, $proponent = null) {
        return Mail::to($email, $proponent ? $proponent->name : '')->bcc('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new ProposalContractMail($proposal, $proponent));
    }

    public function sendMailProposalContractWP($phone, $proposal, $proponent = null) {

        $url = 'https://api.whatsapp.com/send';
        $link = 'https://mg2incorp.com.br/contratos/documento/'.$proposal->file;
        $text = 'Olá '.($proponent ? $proponent->name : '').', estou enviando o link para apreciação e assinatura de seu contrato. Para visualizar é só clicar no link em azul a seguir '.$link.'. Em caso de duvidas me deixo a disposição.';

        return $url.'?phone=55'.onlyNumber($phone).'&text='.$text;
    }

    public function sendMailContact($request) {
        // return Mail::to('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new ContactMail($request));
        return Mail::to('contato@mg2incorp.com.br', 'MG2 Incorp')->bcc('suporte@inovecommerce.com.br', 'MG2 Incorp')->send(new ContactMail($request));
    }

    public function sendMailOutdatedBilling($email, $proposal, $proponent = null) {
        return Mail::to($email, $proponent ? $proponent->name : '')->send(new OutdatedBillingMail($proposal, $proponent));
    }

    public function sendMailBilletWP($phone, $project, $billet) {

        $url = 'https://api.whatsapp.com/send';
        $link = route('billing.billet').'?billet='.$billet->token;
        $barcode = $billet->bar_code;
        $loteamento = $project->name;

        $text = 'Conforme solicitado, segue o boleto referente ao '.$loteamento.'. Para visualizá-lo clique no link em azul a seguir: '.$link.'. Estamos encaminhando também a linha digitável caso deseje já efetuar o pagamento: '.$barcode.' - Atenciosamente Financeiro '.$loteamento;

        return $url.'?phone=55'.onlyNumber($phone).'&text='.$text;
    }

    public function sendMailProposalStatus($type, $user, $proposal) {
        return Mail::to($user->email, $user->name)->send(new ProposalStatusMail($type, $user, $proposal));
    }
}
