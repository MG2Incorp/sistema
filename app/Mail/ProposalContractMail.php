<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProposalContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public $proposal;
    public $proponent;

    public function __construct($proposal, $proponent = null) {
        $this->proposal = $proposal;
        $this->proponent = $proponent;
    }

    public function build() {
        return $this->markdown('emails.proposal_contract')
                    ->subject('CONTRATO - '.$this->proposal->property->block->building->project->name)
                    ->with(['proponent' => $this->proponent])
                    ->attachFromStorageDisk('public', $this->proposal->file);
    }
}