<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailPenghapusan extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;

    public function __construct($nama = '')
    {
        $this->nama = $nama;
    }

    public function build()
    {
        return $this->view('emails.penghapusan_akun')
            ->subject("Informasi Penghapusan Akun")
            ->with([
                'nama' => $this->nama,
            ]);
    }
}
