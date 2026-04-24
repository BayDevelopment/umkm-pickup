<?php

namespace App\Filament\Resources\UMKMS\Pages;

use App\Filament\Resources\UMKMS\UMKMResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateUMKM extends CreateRecord
{
    protected static string $resource = UMKMResource::class;

    protected function getRedirectUrl(): string
    {
        $user = Auth::user();

        if ($user->role === 'owner' && $user->status === 'pending') {
            return '/admin/pending-approval';
        }

        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (Auth::user()->role !== 'admin') {
            $data['user_id'] = Auth::id();
        }

        return $data;
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
        $user = Auth::user();

        $actions = [
            \Filament\Actions\Action::make('create')
                ->label('Kirim Data')
                ->icon('heroicon-o-check-circle')
                ->color('primary')
                ->requiresConfirmation()
                ->modalHeading('Yakin ingin mengirim data?')
                ->modalDescription('Periksa kembali data UMKM Anda. Setelah dikirim dan disetujui admin, data tidak dapat diubah.')
                ->modalSubmitActionLabel('Ya, Kirim Sekarang')
                ->modalCancelActionLabel('Periksa Lagi')
                ->action(fn() => $this->create()),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->url($this->getResource()::getUrl('index'))
                ->icon('heroicon-o-x-mark')
                ->color('gray'),
        ];

        if ($user->role === 'admin') {
            array_splice($actions, 1, 0, [
                $this->getCreateAnotherFormAction()
                    ->label('Create & Create Another')
                    ->icon('heroicon-o-plus-circle')
                    ->color('success'),
            ]);
        }

        return $actions;
    }
}
