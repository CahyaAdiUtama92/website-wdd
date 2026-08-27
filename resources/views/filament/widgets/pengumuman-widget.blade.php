<x-filament-widgets::widget>
    <div class="h-fit bg-[#FFFFFF] rounded-xl p-3 border border-gray-200">
        <h2 class="text-2xl font-bold text-[#2E8B57] leading-tight uppercase py-4">
            Pengumuman
        </h2>
        <div class="flex flex-col gap-4">
            @foreach ($pengumumans as $pengumuman)
                <div class="bg-[#FAFAFA] rounded-lg p-5 flex flex-col md:flex-row md:items-center justify-between gap-4 border border-[#2E8B57]">
                    <div class="flex flex-col gap-2 flex-1">
                        <!-- Title -->
                        <h2 class="text-lg font-bold text-[#09090B] leading-tight uppercase">
                            {{ $pengumuman->judul }}
                        </h2>
                        
                        <!-- Description -->
                        @if($pengumuman->keterangan)
                            <p class="text-[15px] text-[#717683] leading-snug">
                                {{ $pengumuman->keterangan }}
                            </p>
                        @endif
                        
                        <!-- Date and Author -->
                        <div class="text-[14px] font-bold text-[#09090B] mt-1 md:mt-0">
                            {{ $pengumuman->created_at->translatedFormat('d F Y') }} <span class="mx-1">|</span> Oleh {{ $pengumuman->user?->name ?? 'Sistem' }}
                        </div>
                    </div>
                    
                    <!-- Action Button -->
                    @if($pengumuman->file)
                        <a href="{{ Storage::url($pengumuman->file) }}" target="_blank" 
                            class="w-full md:w-32 lg:w-28 h-10 shrink-0 block text-center bg-[#E67E22] hover:bg-[#964c0b] text-[#FFFFFF] font-bold text-[15px] py-3 rounded-lg transition duration-200 uppercase">
                            LIHAT
                        </a>
                    @endif
                </div>
            @endforeach

            @if($pengumumans->isEmpty())
                <div class="bg-[#FAFAFA] rounded-lg p-6 text-center shadow border border-[#2E8B57]">
                    <p class="text-[#717683] text-lg font-medium">Belum ada pengumuman terbaru.</p>
                </div>
            @endif
        </div>
        
        <div class="mt-4">
            {{ $pengumumans->links() }}
        </div>
    </div>
</x-filament-widgets::widget>
