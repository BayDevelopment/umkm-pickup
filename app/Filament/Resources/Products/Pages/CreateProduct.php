<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\ProductImageModel;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    // FILTER ADMIN AND OWNER
    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();
        $user = Auth::user();

        if ($user->role === 'owner') {
            $query->where('umkm_id', $user->umkm_id);
        }

        return $query;
    }

    // ADD menambahkan gambar di form product dan di simpan di table gambar produk
    protected function afterCreate(): void
    {
        $images = $this->data['images_temp'] ?? [];

        foreach ($images as $index => $path) {
            ProductImageModel::create([
                'product_id' => $this->record->id,
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
    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Data product berhasil ditambahkan.')
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
