<?php

namespace App\Filament\Widgets;

use App\Models\CategoryModel;
use App\Models\OrderModel;
use App\Models\ProductModel;
use App\Models\User;
use Carbon\Carbon;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\Auth;

class StatsOverview extends BaseWidget
{
    use InteractsWithPageFilters;

    protected function getStats(): array
    {
        $user = Auth::user();
        $isOwner = $user->role === 'owner';
        $umkmId = $isOwner ? $user->umkm?->id : null;

        $period = $this->filters['period'] ?? 'this_month';
        $now = Carbon::now();

        switch ($period) {
            case 'last_month':
                $start = $now->copy()->subMonthNoOverflow()->startOfMonth();
                $end   = $now->copy()->subMonthNoOverflow()->endOfMonth();
                $label = 'Bulan Kemarin (' . $start->translatedFormat('F Y') . ')';
                break;
            case 'this_year':
                $start = $now->copy()->startOfYear();
                $end   = $now->copy()->endOfYear();
                $label = 'Tahun Ini (' . $now->year . ')';
                break;
            case 'last_year':
                $start = $now->copy()->subYearNoOverflow()->startOfYear();
                $end   = $now->copy()->subYearNoOverflow()->endOfYear();
                $label = 'Tahun Kemarin (' . $start->year . ')';
                break;
            default:
                $start = $now->copy()->startOfMonth();
                $end   = $now->copy()->endOfMonth();
                $label = 'Bulan Ini (' . $start->translatedFormat('F Y') . ')';
                break;
        }

        // Query produk
        $productQuery = ProductModel::query();
        if ($isOwner) $productQuery->where('umkm_id', $umkmId);

        // Query keuntungan
        $revenueQuery = OrderModel::query()->where('status', 'done')->whereBetween('created_at', [$start, $end]);
        if ($isOwner) {
            $revenueQuery->whereHas('items', function ($q) use ($umkmId) {
                $q->whereHas('variant.product', function ($q2) use ($umkmId) {
                    $q2->where('umkm_id', $umkmId);
                });
            });
        }

        $totalRevenue = $revenueQuery->sum('total_price') ?? 0;

        $stats = [
            Stat::make('Produk', number_format($productQuery->count(), 0, ',', '.'))
                ->description('Total Produk')
                ->descriptionIcon('heroicon-o-cube')
                ->color('primary'),

            Stat::make('Keuntungan', 'Rp ' . number_format($totalRevenue, 0, ',', '.'))
                ->description($label)
                ->descriptionIcon('heroicon-o-currency-dollar')
                ->color('danger'),
        ];

        // Hanya admin yang lihat Pengguna & Kategori
        if (!$isOwner) {
            array_splice($stats, 1, 0, [
                Stat::make('Pengguna', number_format(User::count(), 0, ',', '.'))
                    ->description('Total Pengguna')
                    ->descriptionIcon('heroicon-o-users')
                    ->color('success'),

                Stat::make('Kategori', number_format(CategoryModel::count(), 0, ',', '.'))
                    ->description('Total Kategori')
                    ->descriptionIcon('heroicon-o-tag')
                    ->color('warning'),
            ]);
        }

        return $stats;
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
