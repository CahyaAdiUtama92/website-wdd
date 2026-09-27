<x-filament-panels::page>
    @php
        $stats = $this->getStats();
    @endphp

    <div class="grid grid-cols-1 gap-4 md:grid-cols-4 mb-6">
        <x-filament::section>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Anggota Wajib Bayar</div>
            <div class="mt-2 text-2xl font-bold">{{ $stats['total'] }}</div>
            <div class="text-xs text-gray-400 mt-1">Pada periode yang dipilih</div>
        </x-filament::section>
        
        <x-filament::section>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Sudah Membayar</div>
            <div class="mt-2 text-2xl font-bold text-success-600 dark:text-success-400">{{ $stats['sudah'] }}</div>
            <div class="text-xs text-gray-400 mt-1">Pada periode yang dipilih</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Belum Membayar</div>
            <div class="mt-2 text-2xl font-bold text-danger-600 dark:text-danger-400">{{ $stats['belum'] }}</div>
            <div class="text-xs text-gray-400 mt-1">Pada periode yang dipilih</div>
        </x-filament::section>

        <x-filament::section>
            <div class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Nominal Tunggakan</div>
            <div class="mt-2 text-2xl font-bold">Rp {{ number_format($stats['tunggakan'], 0, ',', '.') }}</div>
            <div class="text-xs text-gray-400 mt-1">Sampai {{ $stats['periode_nama'] }}</div>
        </x-filament::section>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
