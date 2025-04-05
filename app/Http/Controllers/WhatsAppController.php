<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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
}
