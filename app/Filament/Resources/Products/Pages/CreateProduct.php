<?php

namespace App\Filament\Resources\Products\Pages;

use App\Filament\Resources\Products\ProductResource;
use App\Models\ProductImageModel;
use App\Models\umkmModel;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateProduct extends CreateRecord
{
    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            $umkm = umkmModel::where('verification_status', 'approved')
                ->where('user_id', $user->id)
                ->first();

            $data['umkm_id'] = $umkm?->id;
        }

        return $data;
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->role === 'owner') {
            $approved = umkmModel::where('verification_status', 'approved')
                ->where('user_id', $user->id)
                ->exists();

            if (!$approved) {
                Notification::make()
                    ->title('UMKM Belum Terverifikasi')
                    ->body('UMKM Anda masih dalam proses verifikasi. Harap tunggu persetujuan admin.')
                    ->warning()
                    ->seconds(5)
                    ->send();

                // ✅ Gunakan redirectRoute bukan $this->redirect()
                $this->redirectRoute('filament.admin.resources.products.index');
                return;
            }
        }

        parent::mount();
    }

    protected function afterCreate(): void
    {
        $images = $this->data['images_temp'] ?? [];

        foreach ($images as $index => $path) {
            $filename = basename($path);
            $newPath = 'products/' . $filename;

            \Illuminate\Support\Facades\Storage::disk('public')
                ->move($path, $newPath);

            ProductImageModel::create([
                'product_id' => $this->record->id,
                'path'       => $newPath,
                'is_main'    => $index === 0,
                'sort_order' => $index,
            ]);
        }
    }

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
