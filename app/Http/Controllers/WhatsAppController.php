<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class WhatsAppController extends Controller
{
    public function sendBulkWhatsapp(array $phones, array $names, string $subject, string $message)
    {
        $whatsappApiUrl = 'https://api.fonnte.com/send';
        $appName = env('APP_NAME');
        $apiKey = env('FONNTE_API_KEY');

        foreach ($phones as $phone) {
            // Membuat pesan lengkap
            $i = array_search($phone, $phones);
            $name = $names[$i];
            $fullMessage = "*{$subject} - {$appName}*\n\nHalo {$name}!,\n{$message}\n\nTerima kasih telah bergabung dengan kami di *{$appName}*! Kami sangat senang Anda memilih layanan kami.";

            // Kirim permintaan ke WhatsApp API menggunakan HTTP dengan Authorization
            $response = Http::withHeaders([
                'Authorization' => $apiKey,
            ])->post($whatsappApiUrl, [
                'target' => $phone,
                'message' => $fullMessage,
                'countryCode' => '62',
            ]);

            // Proses response
            if ($response->successful()) {
                Log::info("Message sent successfully to {$phone}");
            } else {
                Log::error("Error sending WhatsApp message to {$phone}: {$response->body()}");
            }
        }
    }

    public function sendNotaPesanan(string $phone, array $nota, array  $item_nota)
    {
        $whatsappApiUrl = 'https://api.fonnte.com/send';
        $appName = env('APP_NAME');
        $apiKey = env('FONNTE_API_KEY');

        $title = "*Nota Pesanan - {$nota['pesanan_id']}*";
        $tanggal = "*Tanggal Pesanan:* " . \Carbon\Carbon::parse($nota['tanggal'])->format('d F Y');
        $sub = 'Berikut adalah informasi pesanan Anda:';

        $detail_nota = "*Pesanan ID*: {$nota['pesanan_id']}\n"
            . "*Diskon (%)*: {$nota['persentase_diskon']}%\n"
            . "*Diskon (Rp)*: " . number_format($nota['diskon'], 0, ',', '.') . "\n"
            . "*Pajak (%)*: {$nota['persentase_pajak']}%\n"
            . "*Pajak (Rp)*: " . number_format($nota['pajak'], 0, ',', '.') . "\n"
            . "*Total Akhir (Rp)*: " . number_format($nota['total_akhir'], 0, ',', '.');

        $detail_item_nota = "\n\n*Item Pesanan*:\n";
        foreach ($item_nota as $item) {
            $detail_item_nota .= "*Produk*: {$item['produk']}\n"
                . "*Jumlah*: {$item['jumlah']}\n"
                . "*Harga (Rp)*: " . number_format($item['harga'], 0, ',', '.') . "\n"
                . "*Total (Rp)*: " . number_format($item['total'], 0, ',', '.') . "\n\n";
        }

        $closure = "Terima kasih telah bergabung dengan kami di *{$appName}*! Kami sangat senang Anda memilih layanan kami.";

        $fullMessage = $title . "\n\n" . $tanggal . "\n\n" . $sub . "\n\n" . $detail_nota . "\n\n" . $detail_item_nota . "\n\n" . $closure;


        // Kirim permintaan ke WhatsApp API menggunakan HTTP dengan Authorization
        $response = Http::withHeaders([
            'Authorization' => $apiKey,
        ])->post($whatsappApiUrl, [
            'target' => $phone,
            'message' => $fullMessage,
            'countryCode' => '62',
        ]);

        // Proses response
        if ($response->successful()) {
            Log::info("Message sent successfully to {$phone}");
        } else {
            Log::error("Error sending WhatsApp message to {$phone}: {$response->body()}");
        }
    }
}
