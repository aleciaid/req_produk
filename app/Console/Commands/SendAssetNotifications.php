<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Asset;
use App\Services\AssetNotificationService;

class SendAssetNotifications extends Command
{
    protected $signature = 'notifications:send-assets';
    protected $description = 'Send notifications for assets with status not active';

    protected $notificationService;

    public function __construct(AssetNotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    public function handle()
    {
        $assets = Asset::where('Status', '!=', 'Aktif')->get();

        foreach ($assets as $asset) {
            try {
                $this->notificationService->notifyRenewal($asset);
                \Log::info('Notification sent for asset: ' . $asset->nama_asset);
            } catch (\Exception $e) {
                \Log::error('Failed to send notification: ' . $e->getMessage());
            }
        }
    }
}
