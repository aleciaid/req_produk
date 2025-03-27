<?php

namespace App\Services;

use App\Models\AdminNotif;
use App\Models\Product;

class NotificationService
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function notifyNewProduct(Product $product)
    {
        $admin = AdminNotif::where('admin', 'admin1')->first();
        
        if (!$admin) {
            \Log::warning('Admin1 not found for notification');
            return;
        }

        $message = "*Hallo {$admin->nama} ada data baru di product yakni*\n" .
                  "nama: {$product->nama_produk}\n" .
                  "status: {$product->status}\n" .
                  "*mohon segera kerjakan data ini ya*";

        $this->whatsappService->sendMessage($admin->phone, $message);
    }

    public function notifyCompletedProduct(Product $product)
    {
        try {
            $admin = AdminNotif::where('admin', 'admin2')->first();
            
            if (!$admin) {
                \Log::warning('Admin2 not found for notification');
                return;
            }

            \Log::info('Sending completion notification to admin: ' . $admin->nama);

            $message = "*Halo {$admin->nama} data produk ini sudah selesai, tolong segera di cek ya*\n" .
                      "nama: {$product->nama_produk}\n" .
                      "status: {$product->status}\n" .
                      "*mohon segera terbitkan segera ya*";

            $result = $this->whatsappService->sendMessage($admin->phone, $message);
            
            if (!$result) {
                \Log::error('Failed to send WhatsApp message to admin');
            }
        } catch (\Exception $e) {
            \Log::error('Notification error: ' . $e->getMessage());
        }
    }
}