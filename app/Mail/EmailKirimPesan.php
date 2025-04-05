<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailKirimPesan extends Mailable
{
    use Queueable, SerializesModels;

    public $nama;
    public $body_text;
    public $subjek;

    public function __construct($data)
    {
        $this->nama = $data['nama'];
        $this->subjek = $data['subjek'];
        $this->body_text = $data['body_text'];
    }

    public function build()
    {
        return $this->view('emails.kirim_pesan')
            ->with([
                'nama' => $this->nama,
                'subjek' => $this->subjek,
                'body_text' => $this->body_text,
            ])
            ->subject($this->subject);
    }
}
