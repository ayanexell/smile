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
                        'target' => $this->target,   // contoh: 628123456789
                        'message' => $this->message,
                        'delay' => $this->delay
                    ])->json();

            // $response = Http::post(env('WAHA_URL') . '/api/sendText', [
            //     'session' => env('WAHA_SESSION'),
            //     'chatId' => $this->target . '@c.us', // Format nomor: 628xxxxxxxxxx@c.us
            //     'text' => $this->message,
            // ]);
            // dd($response);

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
