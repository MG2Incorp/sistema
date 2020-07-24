<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ContractAvaliableMail extends Mailable
{
    use Queueable, SerializesModels;

    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function build()
    {
        return $this->markdown('emails.contract_available')->subject('Aviso de Contrato Disponível')->with(['id' => $this->id]);
    }
}