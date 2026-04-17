<?php

namespace App\Filament\Resources\UMKMS\Pages;

use App\Filament\Resources\UMKMS\UMKMResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUMKMS extends ListRecords
{
    protected static string $resource = UMKMResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('UMKM')
                ->icon('heroicon-o-plus'),
        ];
    }
}
