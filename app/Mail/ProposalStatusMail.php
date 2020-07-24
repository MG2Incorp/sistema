<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $type;
    public $user;
    public $proposal;

    public function __construct($type, $user, $proposal) {
        $this->type = $type;
        $this->user = $user;
        $this->proposal = $proposal;
    }

    public function build() {

        switch ($this->type) {
            case 'RESERVED':
                $content = 'Proposta '.$this->proposal->id.' do Empreendimento '.$this->proposal->property->block->building->project->name.' em status RESERVADO aguardando documentação para análise. Acesse o sistema MG2 Incorp para maiores informações.';
            break;
            case 'DOCUMENTS_PENDING':
                $content = 'Proposta '.$this->proposal->id.' do Empreendimento '.$this->proposal->property->block->building->project->name.' em status DOCUMENTAÇÃO PENDENTE providencie a regularização o quanto antes. Acesse o sistema MG2 Incorp para maiores informações.';
            break;
            case 'REFUSED':
                $content = 'Proposta '.$this->proposal->id.' do Empreendimento '.$this->proposal->property->block->building->project->name.' em status REPROVADO. Acesse o sistema MG2 Incorp para maiores informações.';
            break;
            case 'CONTRACT_AVAILABLE':
                $content = 'Proposta '.$this->proposal->id.' do Empreendimento '.$this->proposal->property->block->building->project->name.' em status CONTRATO DISPONÍVEL, prossiga com a impressão e assinatura do contrato junto aos PROPONENTES COMPRADORES. Acesse o sistema MG2 Incorp para maiores informações.';
            break;
            default: break;
        }

        return $this->markdown('emails.proposal_status')
                    ->subject('Alteração de Status de Proposta')
                    ->with([ 'content' => $content, 'user' => $this->user ]);
    }
}