<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailPeranBaru extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $peranBaru;

    public function __construct($nama = '', $peranBaru = '')
    {
        $this->nama = $nama;
        $this->peranBaru = $peranBaru;
    }

    public function build()
    {
        return $this->view('emails.peran_baru_akun')
            ->subject("Informasi Peran Baru")
            ->with([
                'nama' => $this->nama,
                'peranBaru' => $this->peranBaru
            ]);
    }
}
