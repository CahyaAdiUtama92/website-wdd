<div>
    {{-- Daftar absensi: tampilan identik dengan Repeater asli, 10 per halaman --}}
    <div class="space-y-2">
        @forelse ($absensis as $absensi)
            <div class="fi-fo-repeater-item rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">

                    {{-- Nama Anggota --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-sm font-medium text-gray-500">
                            Nama Anggota
                        </span>
                        <span class="text-sm font-semibold text-gray-900">
                            {{ $absensi->user?->name ?? '—' }}
                        </span>
                    </div>

                    {{-- Toggle Kehadiran --}}
                    <div class="flex flex-col gap-1">
                        <span class="text-sm font-medium text-gray-500">
                            Kehadiran
                        </span>
                        <div class="flex gap-2">
                            {{-- Tombol Hadir --}}
                            <button
                                type="button"
                                @if (!$absensiDisabled)
                                    wire:click="updateStatus({{ $absensi->id }}, 'Hadir')"
                                @endif
                                @disabled($absensiDisabled)
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold transition-all
                                    {{ $absensi->status === 'Hadir'
                                        ? 'bg-green-600 text-white ring-2 ring-green-400'
                                        : 'bg-green-100 text-green-700' }}
                                    {{ $absensiDisabled ? 'opacity-60 cursor-not-allowed' : 'hover:bg-green-500 hover:text-white cursor-pointer' }}"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                H
                            </button>

                            {{-- Tombol Tidak Hadir --}}
                            <button
                                type="button"
                                @if (!$absensiDisabled)
                                    wire:click="updateStatus({{ $absensi->id }}, 'Tidak Hadir')"
                                @endif
                                @disabled($absensiDisabled)
                                class="inline-flex items-center gap-1.5 rounded-lg px-3 py-1.5 text-sm font-semibold transition-all
                                    {{ $absensi->status === 'Tidak Hadir'
                                        ? 'bg-red-600 text-white ring-2 ring-red-400'
                                        : 'bg-red-100 text-red-700' }}
                                    {{ $absensiDisabled ? 'opacity-60 cursor-not-allowed' : 'hover:bg-red-500 hover:text-white cursor-pointer' }}"
                            >
                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                X
                            </button>
                        </div>
                    </div>

                    {{-- Keterangan --}}
                    <div class="flex flex-col gap-1 sm:col-span-2">
                        <span class="text-sm font-medium text-gray-500">
                            Keterangan
                        </span>
                        @if ($absensiDisabled)
                            <span class="text-sm text-gray-700">
                                {{ $absensi->keterangan ?: '—' }}
                            </span>
                        @else
                            <input
                                type="text"
                                placeholder="opsional"
                                value="{{ $absensi->keterangan }}"
                                wire:change="updateKeterangan({{ $absensi->id }}, $event.target.value)"
                                class="block w-full rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-900 shadow-sm
                                       placeholder:text-gray-400 focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500"
                            />
                        @endif
                    </div>

                </div>
            </div>
        @empty
            <div class="rounded-xl border border-gray-200 bg-white p-6 text-center">
                <p class="text-sm text-gray-500">Belum ada data absensi.</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if ($absensis->hasPages())
        <div class="mt-4">
            {{ $absensis->links() }}
        </div>
    @endif
</div>
