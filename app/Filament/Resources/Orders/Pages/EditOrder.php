<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Events\OrderStatusUpdated;
use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

use Illuminate\Support\Facades\DB;
use App\Models\OrderModel;

class EditOrder extends EditRecord
{
    protected static string $resource = OrderResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->title('Berhasil')
            ->body('Data pesanan berhasil diperbarui.')
            ->success();
    }

    protected function getHeaderActions(): array
    {
        return [
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

    /**
     * Restore stock otomatis jika cancel + rejected
     */
    protected function afterSave(): void
    {
        /** @var OrderModel $order */
        $order = $this->record->load('items.variant');

        // hanya restore jika cancel + rejected dan belum pernah restore
        if (
            $order->status === 'cancel'
            && $order->payment_status === 'rejected'
            && !$order->stock_restored
        ) {

            DB::transaction(function () use ($order) {

                foreach ($order->items as $item) {

                    if ($item->variant) {

                        $item->variant->increment(
                            'stock',
                            $item->quantity
                        );
                    }
                }

                $order->update([
                    'stock_restored' => true
                ]);
            });
        }
    }
}
