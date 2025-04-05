<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $password;

    public function __construct($nama = '', $password = '')
    {
        $this->nama = $nama;
        $this->password = $password;
    }

    public function build()
    {
        return $this->view('emails.reset_password')
            ->subject("Informasi Reset Password")
            ->with([
                'nama' => $this->nama,
                'password' => $this->password,
            ]);
    }
}
