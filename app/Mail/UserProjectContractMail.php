<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserProjectContractMail extends Mailable
{
    use Queueable, SerializesModels;

    public $contract;

    public function __construct($contract)
    {
        $this->contract = $contract;
    }

    public function build()
    {
        return $this->markdown('emails.user_project_contract')->subject('Autorização de Mediação de Venda - '.$this->contract->project->name)->with(['project' => $this->contract->project])->attachFromStorageDisk('public', $this->contract->file);
    }
}