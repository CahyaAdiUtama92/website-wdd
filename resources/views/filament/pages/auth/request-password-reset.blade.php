<div class="fixed inset-0 z-50 flex flex-col items-center justify-center bg-gray-50">
    {{-- Header Logo & Text --}}
    <div class="text-center flex items-center justify-center w-30 h-30 rounded-full overflow-hidden mb-5 border border-[#E67E22] shadow-xl">
        <img src="/images/Logo-WDD.png" alt="Logo Paiketan Warga Dura Desa" style="width: 100%; height: 100%; object-fit: contain;">
    </div>

    {{-- Form Container --}}
    <div class="bg-white rounded-2xl p-6 sm:p-8 shadow-2xl ring-1 ring-gray-900/5 w-100 border border-[#E67E22]">
        @if (! $isSubmitted)
            <h2 class="text-2xl font-bold text-center text-[#2E8B57] mb-2">Lupa Password</h2>
            <p class="text-sm text-gray-500 text-center mb-6">
                Masukkan alamat email yang terdaftar. Kami akan mengirimkan tautan untuk mengatur ulang password Anda.
            </p>

            <form id="form" wire:submit="request">
                {{ $this->form }}

                <x-filament::button type="submit" class="w-full mt-8 py-2.5 text-lg" form="form" style="margin-top: 2rem;">
                    Kirim Link Reset Password
                </x-filament::button>
            </form>
        @else
            <div class="flex justify-center mb-4">
                <svg class="w-16 h-16 text-[#2E8B57]" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-center text-[#2E8B57] mb-2">Periksa Email Anda</h2>
            <p class="text-sm text-gray-500 text-center mb-6">
                Jika alamat email tersebut terdaftar, kami telah mengirimkan tautan untuk mengatur ulang password.
                <br><br>
                Silakan periksa kotak masuk email dan folder Spam Anda.
            </p>
        @endif

        <div class="mt-6 text-center text-sm">
            <a href="{{ filament()->getLoginUrl() }}" class="font-medium text-[#2E8B57] hover:text-[#E67E22]">
                &larr; Kembali ke Login
            </a>
        </div>
    </div>
        
    {{-- Footer --}}
    <div class="items-center mt-8 text-sm font-medium text-gray-500">
        <p class="text-center">
            &copy; {{ date('2024') }} Paiketan Warga Dura Desa Br. Dinas Dajan Ceking.
        </p>
    </div>
</div>
