<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class UserCreateMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $pass;

    public function __construct($email, $pass)
    {
        $this->email = $email;
        $this->pass = $pass;
    }

    public function build()
    {
        return $this->markdown('emails.user_create')->subject('Você foi cadastrado no sistema!')->with(['email' => $this->email, 'pass' => $this->pass]);
    }
}