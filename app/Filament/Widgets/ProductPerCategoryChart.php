<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\CategoryModel;
use Illuminate\Support\Facades\Auth;

class ProductPerCategoryChart extends ChartWidget
{
    protected ?string $heading = 'Jumlah Produk per Category';

    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 6;

    protected function getData(): array
    {
        $user = Auth::user();

        $data = CategoryModel::withCount(['products' => function ($query) use ($user) {
            if ($user->role === 'owner') {
                $query->where('umkm_id', $user->umkm?->id);
            }
        }])->get();

        return [
            'datasets' => [
                [
                    'label' => 'Jumlah Produk',
                    'data' => $data->pluck('products_count'),
                ],
            ],
            'labels' => $data->pluck('name'),
        ];
    }

    protected function getType(): string
    {
        return 'bar';
        // bisa juga: pie, doughnut, line
    }
}
