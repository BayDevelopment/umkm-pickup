<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class PendingApproval extends Page
{
    protected string $view = 'filament.pages.pending-approval';
    protected static bool $shouldRegisterNavigation = false;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-clock';
    }

    public function mount(): void
    {
        $user = Auth::user();

        if ($user->role === 'owner' && $user->status === 'pending') {
            // Kalau belum punya UMKM, redirect ke form daftar UMKM
            if (!$user->umkm) {
                $this->redirect('/admin/u-m-k-m-s/create');
            }
        }
    }
}
