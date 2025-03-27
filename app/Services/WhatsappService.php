<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappService
{
    protected $apiKey;
    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = env('DRIPSENDER_API_KEY');
        $this->baseUrl = 'https://api.dripsender.id/send';
    }

    public function sendMessage($phone, $message)
    {
        try {
            \Log::info('Attempting to send WhatsApp message to: ' . $phone);
            
            $response = Http::post($this->baseUrl, [
                'api_key' => $this->apiKey,
                'phone' => $phone,
                'text' => $message
            ]);

            \Log::info('WhatsApp API Response: ' . $response->body());

            return $response->successful();
        } catch (\Exception $e) {
            \Log::error('WhatsApp notification failed: ' . $e->getMessage());
            return false;
        }
    }
}