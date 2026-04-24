<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\OrderItemModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ProductPurchasedChart extends ChartWidget
{
    protected ?string $heading = 'Produk yang Dibeli (Done)';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 6;

    protected function getData(): array
    {
        $user = Auth::user();
        $isOwner = $user->role === 'owner';
        $umkmId = $isOwner ? $user->umkm?->id : null;

        $data = OrderItemModel::select(
            'product_name',
            DB::raw('SUM(quantity) as total')
        )
            ->whereHas('order', function ($query) {
                $query->where('status', 'done');
            })
            ->when($isOwner, function ($query) use ($umkmId) {
                $query->whereHas('variant.product', function ($q) use ($umkmId) {
                    $q->where('umkm_id', $umkmId);
                });
            })
            ->groupBy('product_name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Dibeli',
                    'data' => $data->pluck('total'),
                ],
            ],
            'labels' => $data->pluck('product_name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
