<div class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-gray-50">
    {{-- Header Logo & Text --}}
    <div class="text-center flex items-center justify-center w-30 h-30 rounded-full overflow-hidden mb-5 border border-[#E67E22] shadow-xl">
        <img src="/images/Logo-WDD.png" alt="Logo Paiketan Warga Dura Desa" style="width: 100%; height: 100%; object-fit: contain;">
    </div>

    {{-- Form Container --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-2xl ring-1 ring-gray-900/5 w-100 border border-[#E67E22]">
        <form id="form" wire:submit="authenticate">
            {{ $this->form }}

            <x-filament::button type="submit" class="w-full mt-8 py-2.5 text-lg" form="form" style="margin-top: 2rem;">
                Masuk
            </x-filament::button>
        </form>
    </div>
        
    {{-- Footer --}}
    <div class="items-center mt-8 text-sm font-medium text-gray-500">
        <p class="text-center">
            &copy; {{ date('2024') }} Paiketan Warga Dura Desa Br. Dinas Dajan Ceking.
        </p>
    </div>
</div>
