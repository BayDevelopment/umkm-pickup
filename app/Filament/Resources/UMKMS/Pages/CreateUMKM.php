<?php

namespace App\Filament\Resources\UMKMS\Pages;

use App\Filament\Resources\UMKMS\UMKMResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUMKM extends CreateRecord
{
    protected static string $resource = UMKMResource::class;


    // ambil id user yg login untuk membuat umkm
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Auth::user()->role !== 'admin') {
            $data['user_id'] = Auth::id();
        }

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Data UMKM berhasil ditambahkan.')
            ->success();
    }

    protected function getFormActions(): array
    {
        return [

            $this->getCreateFormAction()
                ->label('Create')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCreateAnotherFormAction()
                ->label('Create & Create Another')
                ->icon('heroicon-o-plus-circle')
                ->color('success'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-x-mark')
                ->color('gray'),

        ];
    }
}
