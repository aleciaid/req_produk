<?php

namespace App\Filament\Resources\AdminNotifResource\Pages;

use App\Filament\Resources\AdminNotifResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAdminNotifs extends ListRecords
{
    protected static string $resource = AdminNotifResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
