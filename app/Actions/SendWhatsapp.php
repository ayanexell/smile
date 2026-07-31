<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Exception;

use Illuminate\Support\Facades\Log;
class SendWhatsapp
{
    protected string $target;
    protected string $message;

    public function __construct(string $target, string $message)
    {
        $this->target = $target;
        $this->message = $message;
    }

    /**
     * Kirim pesan melalui Fonnnte API.
     *
     * @return Response
     * @throws Exception
     */
    public function send(): Response
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => config('app.fonnte.token'),
            ])->post(config('app.fonnte.endpoint'), [
                        'target' => $this->target,
                        'message' => $this->message,
                    ]);

            $result = $response->json();

            if (!$result['status']) {
                $errorMsg = $result['message'] ?? 'Gagal mengirim pesan WhatsApp.';
                throw new Exception($errorMsg, $response->status());
            }

            return $response;

        } catch (Exception $e) {
            // Lempar kembali exception atau log errornya
            throw new Exception("WhatsApp Error: " . $e->getMessage());
        }
    }
}
