<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailNotaPesanan extends Mailable
{
    use SerializesModels, Queueable;

    public $nota;
    public $item_nota;

    /**
     * Create a new message instance.
     *
     * @param array $nota
     * @param array $item_nota
     */
    public function __construct($nota, $item_nota)
    {
        $this->nota = $nota;
        $this->item_nota = $item_nota;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Nota Pesanan - " . $this->nota['pesanan_id'])
            ->view('emails.nota_pesanan')
            ->with([
                'nota' => $this->nota,
                'item_nota' => $this->item_nota,
            ]);
    }
}
