<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserProjectMail extends Mailable
{
    use Queueable, SerializesModels;

    public $proj;

    public function __construct($proj)
    {
        $this->proj = $proj;
    }

    public function build()
    {
        return $this->markdown('emails.user_project')->subject('Você foi vinculado a um empreendimento!')->with(['project' => $this->proj]);
    }
}