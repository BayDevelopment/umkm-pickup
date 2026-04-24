<x-filament-panels::page>
    <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">

        <div class="space-y-3">
            <h1 class="text-xl font-bold text-gray-950 dark:text-white">
                Akun Anda sedang menunggu persetujuan
            </h1>
            <p class="text-gray-500 dark:text-gray-400">
                Silakan tunggu admin memverifikasi UMKM Anda.
            </p>
        </div>

        <form action="{{ url('/admin/logout') }}" method="POST" class="mt-8" style="margin-top: 10px">
            @csrf
            <x-filament::button type="submit" color="danger" icon="heroicon-m-arrow-left-on-rectangle" size="lg">
                Keluar
            </x-filament::button>
        </form>

    </div>
</x-filament-panels::page>
