<?php

namespace App\Filament\Resources\UMKMS\Pages;

use App\Filament\Resources\UMKMS\UMKMResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUMKM extends EditRecord
{
    protected static string $resource = UMKMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
