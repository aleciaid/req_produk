<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AdminNotif;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AssetNotificationService
{
    protected $whatsappService;

    public function __construct(WhatsappService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    public function notifyRenewal(Asset $asset)
    {
        $admins = AdminNotif::whereIn('admin', ['admin1', 'admin3'])->get();
        $daysLeft = Carbon::now()->diffInDays($asset->tanggal_perpanjangan, false);

        if ($admins->isEmpty()) {
            Log::warning('No admins found for notification.');
            return;
        }

        foreach ($admins as $admin) {
            $message = "Data aset produk ini sudah saatnya perpanjangan, mohon segera lakukan pembayaran atau perpanjangan dengan sisa waktu {$daysLeft} hari.\n\n" .
                       "Nama produk: {$asset->nama_asset}\n" .
                       "Harga: " . number_format($asset->harga) . "\n" .
                       "Tanggal: {$asset->tanggal_perpanjangan}\n" .
                       "Tipe: {$asset->Tipe}\n" .
                       "Status: {$asset->Status}";

            try {
                $this->whatsappService->sendMessage($admin->phone, $message);
                Log::info("Notification sent to admin: {$admin->nama}");
            } catch (\Exception $e) {
                Log::error("Failed to send notification to admin: {$admin->nama}. Error: " . $e->getMessage());
            }
        }
    }
}