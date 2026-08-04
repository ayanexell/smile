<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\Response;
use Exception;

use Illuminate\Support\Facades\Log;
class WhatsappAction
{
    protected string $target;
    protected string $message;
    protected int $delay = 0;

    public function __construct(string $target, string $message, int $delay = 0)
    {
        $this->target = $target;
        $this->message = $message;
        $this->delay = $delay;
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
                        'delay' => $this->delay
                    ])->json();

            if (!$response['status']) {
                $errorMsg = $response['message'] ?? 'Gagal mengirim pesan WhatsApp.';
                throw new Exception($errorMsg, $response['status']);
            }

            return $response;

        } catch (Exception $e) {
            // Lempar kembali exception atau log errornya
            throw new Exception("WhatsApp Error: " . $e->getMessage());
        }
    }
}
