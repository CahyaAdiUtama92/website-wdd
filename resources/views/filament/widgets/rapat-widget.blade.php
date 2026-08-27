<x-filament-widgets::widget>
    <div class="h-fit bg-[#FFFFFF] rounded-xl p-3 border border-gray-200">
        <h2 class="text-2xl font-bold text-[#2E8B57] leading-tight uppercase py-4">
            Jadwal Rapat Terdekat
        </h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @if($rapat)
            <!-- Rapat Card -->
            <div class="bg-[#E67E22] text-white rounded-xl p-6 shadow-sm flex flex-col justify-between">
                <div>
                    <h2 class="text-2xl font-bold leading-tight">{{ $rapat->judul }}</h2>
                    <div class="mt-2 text-[15px] font-semibold flex items-center">
                        {{ \Carbon\Carbon::parse($rapat->tanggal)->translatedFormat('d F Y') }} <span class="mx-2 font-light">|</span> {{ \Carbon\Carbon::parse($rapat->waktu)->format('H:i') }} - Selesai
                    </div>
                    <div class="mt-1 text-[15px] flex items-center">
                        <span class="mr-2">&bull;</span> Tempat {{ $rapat->tempat }}
                    </div>
                </div>
                
                <div class="mt-6">
                    <hr class="border-white/50 mb-4" />
                    <h3 class="font-bold text-lg">Agenda</h3>
                    <p class="text-[15px] mt-1 line-clamp-2">
                        {{ Str::limit(strip_tags($rapat->agenda), 100) }}
                    </p>
                </div>
            </div>
            @else
            <!-- Empty State Card -->
            <div class="bg-[#FAFAFA] rounded-xl p-6 flex flex-col items-center justify-center border border-[#2E8B57] shadow-sm h-full min-h-[200px]">
                <p class="text-[#717683] text-lg font-medium">Belum ada rapat terdekat saat ini.</p>
            </div>
            @endif

            <!-- Archive Card -->
            <a href="{{ route('filament.admin.resources.rapats.index') }}" class="bg-[#2E8B57] hover:bg-[#257348] text-white rounded-xl p-6 shadow-sm flex flex-col items-center justify-center transition duration-200 h-full min-h-[200px]">
                <h2 class="text-2xl font-bold mb-5 text-center">Lihat Arsip Rapat</h2>
                <div class="bg-white rounded-xl p-3 flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-12 h-12 text-[#2E8B57]">
                        <path d="M3.375 3C2.339 3 1.5 3.84 1.5 4.875v.75c0 1.036.84 1.875 1.875 1.875h17.25c1.035 0 1.875-.84 1.875-1.875v-.75C22.5 3.839 21.66 3 20.625 3H3.375Z" />
                        <path fill-rule="evenodd" d="M3.087 9l.54 9.176A3 3 0 0 0 6.62 21h10.757a3 3 0 0 0 2.995-2.824L20.913 9H3.087Zm6.133 2.845a.75.75 0 0 1 1.06 0l1.72 1.72V9.75a.75.75 0 0 1 1.5 0v3.815l1.72-1.72a.75.75 0 1 1 1.06 1.06l-3 3a.75.75 0 0 1-1.06 0l-3-3a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" />
                    </svg>
                </div>
            </a>
        </div>
    </div>
</x-filament-widgets::widget>
