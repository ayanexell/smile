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
    protected ?string $filePath;
    protected int $delay = 0;

    public function __construct(string $target, string $message, string $filePath = null, int $delay = 0)
    {
        $this->target = $target;
        $this->message = $message;
        $this->filePath = $filePath;
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
        $url = config('app.goowa.endpoint');
        $credentials = explode(':', config('app.goowa.credentials'), 2);
        $username = $credentials[0] ?? 'admin';
        $password = $credentials[1] ?? 'admin';
        try {
            $request = Http::withBasicAuth($username, $password)
                ->withHeaders([
                    'X-Device-Id' => config('app.goowa.device_id'),
                ]);

            // 4. Jika ada file, kirim sebagai Multipart Form Data
            if ($this->filePath && File::exists($this->filePath)) {
                $response = $request->attach(
                    'file',
                    file_get_contents($this->filePath),
                    basename($this->filePath)
                )->post($url, [
                    'phone'   => $this->target,
                    'message' => $this->message,
                ]);
            } else {
                $response = $request->post($url, [
                    'phone'   => $this->target,
                    'message' => $this->message,
                ]);
            }

            // if($response->failed()) {
            //     throw new Exception("Failed to send WhatsApp message. Response: " . $response->body());
            // }
            Log::info("status response: " . $response->failed());
            return $response;

        } catch (Exception $e) {
            // Lempar kembali exception atau log errornya
            throw new Exception("WhatsApp Error: " . $e->getMessage());
        }
    }
}
