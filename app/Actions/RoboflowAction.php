<?php

namespace App\Actions;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class RoboflowAction
{
    protected string $endpoint;
    protected string $apiKey;

    /**
     * Master Data Konstanta untuk Mapping Tipe Barang
     */
    public static array $itemTypes = [
        // --- ELEKTRONIK & PERANGKAT ---
        'AC Portable' => 'Elektronik / Pendingin',
        'Kipas Angin' => 'Elektronik / Pendingin',
        'Teko Listrik' => 'Elektronik / Rumah Tangga',
        'Senter' => 'Elektronik / Alat Penerangan',

        // --- AUDIO & KOMUNIKASI ---
        'Sound Portable' => 'Audio & Komunikasi',
        'Sound System' => 'Audio & Komunikasi',
        'Walkie Talkie' => 'Audio & Komunikasi',

        // --- PERALATAN KANTOR, EDUKASI & PRESENTASI ---
        'Printer' => 'Peralatan Kantor / Fasilitas',
        'Proyektor' => 'Peralatan Kantor / Fasilitas',
        'Papan Tulis' => 'Peralatan Kantor / Fasilitas',

        // --- FURNITUR ---
        'Kursi Meja' => 'Furnitur & Mebel',

        // --- AKSESORI & PERLENGKAPAN LAINNYA ---
        'Tripod' => 'Aksesori Pendukung',
        'Galon Air' => 'Perlengkapan Umum',
    ];

    public function __construct(?string $endpoint = null, ?string $apiKey = null)
    {
        $this->endpoint = $endpoint ?? config('services.roboflow.endpoint');
        $this->apiKey = $apiKey ?? config('services.roboflow.api_key');
    }

    /**
     * Analisis gambar dari path lokal (file upload).
     */
    public function analyzeFromPath(string $imagePath): array
    {
        if (!file_exists($imagePath)) {
            throw new Exception("File gambar tidak ditemukan: {$imagePath}");
        }

        $imageData = base64_encode(file_get_contents($imagePath));

        return $this->send($imageData);
    }

    /**
     * Analisis gambar dari string Base64.
     */
    public function analyzeFromBase64(string $base64Image): array
    {
        return $this->send($base64Image);
    }

    /**
     * Kirim request ke Roboflow dan lakukan mapping tipe secara manual.
     */
    protected function send(string $base64Image): array
    {
        if (empty($this->endpoint) || empty($this->apiKey)) {
            throw new Exception('Konfigurasi Roboflow (endpoint/api_key) belum diatur.');
        }

        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $this->apiKey,
            ])->timeout(30)->post($this->endpoint, [
                        'inputs' => [
                            'image' => [
                                'type' => 'base64',
                                'value' => $base64Image,
                            ],
                        ],
                    ]);

            if (!$response->successful()) {
                Log::error('Roboflow API Error: ' . $response->body());
                throw new Exception('Roboflow API gagal: ' . $response->body());
            }

            $data = $response->json();

            // Mengambil output workflows dari array pertama [0]
            $outputs = $data['outputs'][0] ?? [];

            $predictions = $outputs['detections_filter_output']['predictions'] ?? [];
            $count_object = $outputs['property_definition_output'] ?? 0;

            // Ambil objek pertama jika ada deteksi objek masuk
            $firstPrediction = $predictions[0] ?? null;
            $firstClass = $firstPrediction['class'] ?? null;

            // PROSES MAPPING MANUAL: Ambil tipe berdasarkan class name dari konstanta $itemTypes
            $type = null;
            if ($firstClass !== null) {
                $type = self::$itemTypes[$firstClass] ?? 'Lainnya';
            }

            // Kembalikan format ringkas persis seperti ekspektasi Anda
            return [
                'first_class' => $firstClass, // Diambil dari object detection Roboflow
                'type' => $type,       // Diambil dari hasil map pada konstanta Class
                'count' => $count_object, // Hasil hitung dari Property Definition
                'raw' => $data,       // Data mentah untuk kebutuhan debugging
            ];

        } catch (Exception $e) {
            Log::error('Roboflow Exception: ' . $e->getMessage());
            throw $e;
        }
    }
}
