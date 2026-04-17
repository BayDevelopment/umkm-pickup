<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

class EditProduct extends EditRecord
{
    protected static string $resource = ProductResource::class;

    // FILTER OWNER AND ADMIN
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (Auth::user()->role === 'owner') {
            $data['umkm_id'] = Auth::user()->umkm_id;
        }

        return $data;
    }

    // ADD menambahkan gambar di form product dan di simpan di table gambar produk
    protected function afterSave(): void
    {
        if (!isset($this->data['images_temp'])) return;

        // hapus lama (opsional)
        $this->record->images()->delete();

        foreach ($this->data['images_temp'] as $index => $path) {
            $this->record->images()->create([
                'path' => $path,
                'is_main' => $index === 0,
                'sort_order' => $index,
            ]);
        }
    }
    // ADD menambahkan gambar di form product dan di simpan di table gambar produk

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil Diupdate')
            ->body('Data produk berhasil diperbarui.')
            ->success();
    }


    protected function getHeaderActions(): array
    {
        return [
            // ViewAction::make(),
            // DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
            Action::make('back')
                ->label('Kembali')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),
        ];
    }

    protected function getFormActions(): array
    {
        return [

            $this->getSaveFormAction()
                ->label('Save Changes')
                ->icon('heroicon-o-check-circle')
                ->color('primary'),

            $this->getCancelFormAction()
                ->label('Cancel')
                ->icon('heroicon-o-x-mark')
                ->color('gray')
                ->url(static::getResource()::getUrl('index')),

        ];
    }
}
