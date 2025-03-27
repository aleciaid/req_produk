<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use App\Services\AssetNotificationService;

class Asset extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'nama_asset',
        'deskripsi',
        'kategori',
        'harga',
        'tanggal_pembelian',
        'tanggal_perpanjangan',
        'Tipe',
        'Status'
    ];

    protected static function booted()
    {
        static::saving(function ($asset) {
            $asset->checkAndUpdateStatus();
        });
    }

    public function checkAndUpdateStatus()
    {
        if ($this->Tipe === 'Berlangganan' && $this->tanggal_perpanjangan) {
            $today = Carbon::now();
            $renewalDate = Carbon::parse($this->tanggal_perpanjangan);
            $daysUntilRenewal = $today->diffInDays($renewalDate, false);

            if ($daysUntilRenewal <= 40 && $this->Status !== 'Perpanjangan') {
                $this->Status = 'Perpanjangan';
                $this->notifyAdmins();
            }
        }
    }

    protected function notifyAdmins()
    {
        $notificationService = app(AssetNotificationService::class);
        $notificationService->notifyRenewal($this);
    }
}