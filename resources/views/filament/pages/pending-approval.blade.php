<x-filament-panels::page>
    <div class="text-center">
        <h1 class="text-xl font-bold text-gray-950 dark:text-white">
            Akun Anda sedang menunggu persetujuan
        </h1>

        <p class="mt-2 text-gray-500 dark:text-gray-400">
            Silakan tunggu admin memverifikasi UMKM Anda.
        </p>

        <form action="{{ url('/admin/logout') }}" method="POST" class="mt-6 flex flex-col items-center gap-5">
            @csrf

            <x-filament::button type="submit" color="danger" icon="heroicon-m-arrow-left-on-rectangle">
                Keluar
            </x-filament::button>

        </form>
    </div>
</x-filament-panels::page>
