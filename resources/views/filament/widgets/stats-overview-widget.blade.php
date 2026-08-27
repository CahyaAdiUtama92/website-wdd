<x-filament-widgets::widget>
    <div class="h-fit bg-[#FFFFFF] rounded-xl p-3 border border-gray-200">
        <h2 class="text-2xl font-bold text-[#2E8B57] leading-tight uppercase py-4">
            Keuangan
        </h2>
        
        <div class="flex flex-col gap-6">
            @if($keuangan)
            <div>
                <h3 class="text-lg font-bold text-[#09090B] uppercase mb-3 border-b-2 border-[#2E8B57] inline-block">Kewajiban Pribadi</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Total Kewajiban -->
                    <div class="bg-[#FAFAFA] rounded-lg p-5 border border-[#2E8B57] flex flex-col justify-center">
                        <div class="text-[#717683] text-sm font-bold uppercase tracking-wider mb-1">Total Kewajiban</div>
                        <div class="text-2xl font-bold text-[#09090B]">Rp {{ number_format($keuangan['total_kewajiban'], 0, ',', '.') }}</div>
                        <div class="text-sm text-[#E67E22] font-semibold mt-1">{{ $keuangan['jumlah_bulan_kewajiban'] }} bulan kewajiban</div>
                    </div>

                    <!-- Sudah Dibayar -->
                    <div class="bg-[#FAFAFA] rounded-lg p-5 border border-[#2E8B57] flex flex-col justify-center">
                        <div class="text-[#717683] text-sm font-bold uppercase tracking-wider mb-1">Sudah Dibayar</div>
                        <div class="text-2xl font-bold text-[#09090B]">Rp {{ number_format($keuangan['terbayarkan'], 0, ',', '.') }}</div>
                        <div class="text-sm text-[#2E8B57] font-semibold mt-1">Total iuran yang sudah terbayarkan</div>
                    </div>

                    <!-- Sisa Hutang -->
                    <div class="bg-[#FAFAFA] rounded-lg p-5 border border-[#2E8B57] flex flex-col justify-center">
                        <div class="text-[#717683] text-sm font-bold uppercase tracking-wider mb-1">Sisa Hutang</div>
                        <div class="text-2xl font-bold text-[#09090B]">Rp {{ number_format($keuangan['sisa_hutang'], 0, ',', '.') }}</div>
                        <div class="text-sm {{ $keuangan['sisa_hutang'] > 0 ? 'text-[#E67E22]' : 'text-[#2E8B57]' }} font-semibold mt-1">
                            {{ $keuangan['sisa_hutang'] > 0 ? 'Segera lunasi iuran Anda' : 'Iuran Anda lunas!' }}
                        </div>
                    </div>
                </div>
            </div>
            @endif

            @if($showGlobalStats && $stats)
            <div>
                <h3 class="text-lg font-bold text-[#09090B] uppercase mb-3 border-b-2 border-[#2E8B57] inline-block">Statistik Organisasi</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Total Anggota -->
                    <div class="bg-[#2E8B57] text-white rounded-lg p-5 flex flex-col justify-center shadow-sm">
                        <div class="text-white/80 text-sm font-bold uppercase tracking-wider mb-1">Total Anggota</div>
                        <div class="text-2xl font-bold">{{ $stats['total_anggota'] }}</div>
                        <div class="text-sm font-medium mt-1 text-white/90">Total anggota aktif</div>
                    </div>

                    <!-- Total Kas -->
                    <div class="bg-[#E67E22] text-white rounded-lg p-5 flex flex-col justify-center shadow-sm"><a href="{{ route('filament.admin.resources.arus-kas.index') }}">
                        <div class="text-white/80 text-sm font-bold uppercase tracking-wider mb-1">Total Kas</div>
                        <div class="text-2xl font-bold">Rp {{ number_format($stats['saldo_kas'], 0, ',', '.') }}</div>
                        <div class="text-sm font-medium mt-1 text-white/90">Saldo kas organisasi</div>
                    </a></div>

                    <!-- Iuran Bulan Ini (Hanya Bendahara & Super Admin) -->
                    @if($user && ($user->is_super_admin || $user->role?->name === 'bendahara'))
                    <div class="bg-[#FAFAFA] rounded-lg p-5 border border-[#2E8B57] flex flex-col justify-center">
                        <div class="text-[#717683] text-sm font-bold uppercase tracking-wider mb-1">Iuran Bulan Ini</div>
                        <div class="text-2xl font-bold text-[#09090B]">{{ $stats['iuran_bulan_berjalan']['lunas'] }} / {{ $stats['iuran_bulan_berjalan']['total'] }} Anggota</div>
                        <div class="text-sm text-[#2E8B57] font-semibold mt-1">Sudah membayar iuran {{ now()->translatedFormat('F Y') }}</div>
                    </div>
                    @endif

                    <!-- Total Pemasukan -->
                    <div class="bg-[#FAFAFA] rounded-lg p-5 border border-[#2E8B57] flex flex-col justify-center"><a href="{{ route('filament.admin.resources.arus-kas.index') }}">
                        <div class="text-[#717683] text-sm font-bold uppercase tracking-wider mb-1">Total Pemasukan</div>
                        <div class="text-2xl font-bold text-[#09090B]">Rp {{ number_format($stats['total_pemasukan'], 0, ',', '.') }}</div>
                        <div class="text-sm text-[#2E8B57] font-semibold mt-1">Seluruh pemasukan</div>
                    </a></div>

                    <!-- Total Pengeluaran -->
                    <div class="bg-[#FAFAFA] rounded-lg p-5 border border-[#2E8B57] flex flex-col justify-center"><a href="{{ route('filament.admin.resources.arus-kas.index') }}">
                        <div class="text-[#717683] text-sm font-bold uppercase tracking-wider mb-1">Total Pengeluaran</div>
                        <div class="text-2xl font-bold text-[#09090B]">Rp {{ number_format($stats['total_pengeluaran'], 0, ',', '.') }}</div>
                        <div class="text-sm text-[#E67E22] font-semibold mt-1">Seluruh pengeluaran</div>
                    </a></div>

                    <!-- Donasi -->
                    <div class="bg-[#FAFAFA] rounded-lg p-5 border border-[#2E8B57] flex flex-col justify-center"><a href="{{ route('filament.admin.resources.arus-kas.index') }}">
                        <div class="text-[#717683] text-sm font-bold uppercase tracking-wider mb-1">Donasi {{ now()->year }}</div>
                        <div class="text-2xl font-bold text-[#09090B]">Rp {{ number_format($stats['donasi_tahun_berjalan'], 0, ',', '.') }}</div>
                        <div class="text-sm text-[#2E8B57] font-semibold mt-1">Total donasi tahun {{ now()->year }}</div>
                    </a></div>
                </div>
            </div>
            @endif
        </div>
    </div>
</x-filament-widgets::widget>
