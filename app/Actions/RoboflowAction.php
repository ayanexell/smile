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
     * Mapping kelas objek dari bahasa Inggris ke Bahasa Indonesia.
     * Tambahkan sebanyak mungkin sesuai kelas yang dilatih di model Roboflow Anda.
     */
    protected array $classTranslations = [
        // Kendaraan
        'car' => 'Mobil',
        'truck' => 'Truk',
        'bus' => 'Bus',
        'motorcycle' => 'Sepeda Motor',
        'motorbike' => 'Sepeda Motor',
        'bicycle' => 'Sepeda',
        'bike' => 'Sepeda',
        'van' => 'Van',
        'pickup' => 'Pikap',
        'taxi' => 'Taksi',

        // Peralatan kantor & elektronik
        'laptop' => 'Laptop',
        'computer' => 'Komputer',
        'monitor' => 'Monitor',
        'keyboard' => 'Papan Ketik',
        'mouse' => 'Mouse',
        'printer' => 'Printer',
        'projector' => 'Proyektor',
        'phone' => 'Telepon',
        'cell phone' => 'Telepon Seluler',
        'tablet' => 'Tablet',
        'tv' => 'Televisi',
        'television' => 'Televisi',
        'camera' => 'Kamera',
        'headphone' => 'Headphone',
        'speaker' => 'Pengeras Suara',

        // Furnitur
        'chair' => 'Kursi',
        'table' => 'Meja',
        'desk' => 'Meja Kerja',
        'sofa' => 'Sofa',
        'bed' => 'Tempat Tidur',
        'cabinet' => 'Lemari',
        'bookshelf' => 'Rak Buku',
        'bench' => 'Meja Panjang',

        // Alat tulis & lainnya
        'book' => 'Buku',
        'pen' => 'Pulpen',
        'pencil' => 'Pensil',
        'notebook' => 'Buku Catatan',
        'bag' => 'Tas',
        'backpack' => 'Ransel',
        'bottle' => 'Botol',
        'cup' => 'Cangkir',
        'glass' => 'Gelas',

        // Elektronik rumah tangga
        'fan' => 'Kipas Angin',
        'air conditioner' => 'Pendingin Ruangan',
        'ac' => 'Pendingin Ruangan',
        'refrigerator' => 'Kulkas',
        'microwave' => 'Microwave',
        'oven' => 'Oven',
        'lamp' => 'Lampu',

        // Bangunan
        'door' => 'Pintu',
        'window' => 'Jendela',
        'wall' => 'Dinding',
        'floor' => 'Lantai',
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
     * Kirim request ke Roboflow dan kembalikan hasil dengan terjemahan.
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
            $predictions = $data['outputs'][0]['model_output']['predictions'] ?? [];

            // Terjemahkan setiap prediksi
            $translatedPredictions = array_map(function ($prediction) {
                $class = $prediction['class'] ?? 'unknown';
                $prediction['class_original'] = $class;
                $prediction['class'] = $this->translateClass($class);
                return $prediction;
            }, $predictions);

            return [
                'predictions' => $translatedPredictions,
                'count' => count($translatedPredictions),
                'first_class' => $translatedPredictions[0]['class'] ?? null,
                'raw' => $data,
            ];
        } catch (Exception $e) {
            Log::error('Roboflow Exception: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Terjemahkan satu kelas objek ke Bahasa Indonesia.
     * Mengembalikan nama asli (Title Case) bila tidak ada di kamus.
     */
    public function translateClass(string $class): string
    {
        $key = strtolower(trim($class));

        if (isset($this->classTranslations[$key])) {
            return $this->classTranslations[$key];
        }

        // Jika tidak ada padanan, gunakan huruf kapital awal kata sebagai fallback
        return ucwords($class);
    }

    /**
     * Tambahkan mapping baru pada runtime (opsional).
     */
    public function addTranslation(string $english, string $indonesian): self
    {
        $this->classTranslations[strtolower(trim($english))] = $indonesian;
        return $this;
    }
}
