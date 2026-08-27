<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ config('app.name', 'WDD') }} - Landing</title>
    
    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-gray-50 font-sans">
    
    {{-- Navigation Bar --}}
    <nav class="flex items-center justify-between px-6 py-4 bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <a href="/" class="text-xl font-bold bg-linear-to-r from-green-600 to-orange-500 bg-clip-text text-transparent">Paiketan Warga Dura Desa</a>
        <div class="flex space-x-6 items-center">
            @auth
                <a href="{{ url('/admin') }}" class="text-gray-600 font-medium hover:text-gray-900 transition">Dashboard</a>
            @else
                <a href="{{ route('filament.admin.auth.login') }}" class="text-gray-600 font-medium hover:text-gray-900 transition">Log in</a>
            @endauth
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="bg-center bg-no-repeat bg-cover bg-gray-700 bg-blend-multiply" style="background-image: url('{{ asset('images/bg-hero.png') }}');">
        <div class="px-4 mx-auto max-w-7xl text-center py-24 lg:py-56">
            <h1 class="mb-4 text-4xl font-extrabold tracking-tight leading-none text-white md:text-5xl lg:text-6xl">Selamat Datang</h1>
            <p class="mb-8 text-lg font-normal text-gray-300 lg:text-xl sm:px-16 lg:px-48">Website Resmi Organisasi Paiketan Warga Dura Desa (WDD), Banjar Adat Dajan Ceking, Desa Belatungan.</p>
            <div class="flex flex-col space-y-4 sm:flex-row sm:justify-center sm:space-y-0">
                <a href="{{ route('filament.admin.auth.login') }}" class="inline-flex justify-center hover:text-gray-900 items-center py-3 px-5 sm:ms-4 text-base font-medium text-center text-white rounded-lg border border-white hover:bg-gray-100 focus:ring-4 focus:ring-gray-400 transition">
                    Masuk Dashboard
                </a>
            </div>
        </div>
    </section>

    {{-- About Section --}}
    <section class="bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-28 space-y-10">
            <h2 class="text-4xl font-extrabold text-gray-950 text-center md:text-left">Tentang</h2>
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 items-center">
                <div class="md:col-span-4 flex justify-center">
                    <img src="{{ asset('images/Logo-WDD.png') }}" alt="Logo Paiketan Warga Dura Desa" class="w-64 h-auto drop-shadow-xl rounded-full hover:scale-105 transition-transform duration-300">
                </div>
                <div class="md:col-span-8">
                    <p class="text-gray-900 text-lg md:text-xl text-justify leading-relaxed">
                        Organisasi Paiketan Warga Dura Desa merupakan organisasi sosial masyarakat yang terletak di Banjar Adat Dajan Ceking, Desa Belatungan, Kecamatan Pupuan, Kabupaten Tabanan. Organisasi ini beranggotakan 63 orang yang semua anggotanya merupakan warga Banjar Adat Dajan Ceking yang telah merantau keluar desa atau yang telah berdomisili di luar Desa Belatungan. Organisasi ini didirikan pada tahun 2009 dengan tujuan untuk meningkatkan persatuan dan kebersamaan para warga luar desa agar bisa bersama – bersama dalam membantu dan menunjang segala kegiatan yang ada di lingkungan banjar adat, khususnya Banjar Adat Dajan Ceking, baik itu berupa kegiatan pembangunan balai banjar, meningkatkan komunikasi dan koordinasi kepada prajuru banjar adat untuk mendukung kelancaran pembangunan di banjar adat ini, membantu organisasi STT dalam upaya pembuatan ogoh – ogoh saat Hari Raya Nyepi, serta apabila terdapat upacara adat atau piodalan di pura kayangan maka para anggota yang memiliki kesempatan akan siap membantu.
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Struktur Section --}}
    <section class="bg-white">
        <div class="max-w-7xl mx-auto px-4 py-28 space-y-10">
            <h2 class="text-4xl font-extrabold text-gray-950 text-center md:text-left">Struktur Organisasi</h2>
            
            @php
                $bagan = [
                    'PENANGGUNG JAWAB' => [],
                    'PENASIHAT' => [],
                    'KETUA' => [],
                    'SEKRETARIS' => [],
                    'BENDAHARA' => [],
                    'ANGGOTA' => [],
                ];
                foreach($strukturs as $record) {
                    $jabatanName = strtoupper($record->jabatan->name ?? '');
                    
                    if (str_contains($jabatanName, 'PENANGGUNG JAWAB') || str_contains($jabatanName, 'KELIAN')) {
                        $bagan['PENANGGUNG JAWAB'][] = $record;
                    } elseif (str_contains($jabatanName, 'PENASIHAT')) {
                        $bagan['PENASIHAT'][] = $record;
                    } elseif (str_contains($jabatanName, 'KETUA')) {
                        $bagan['KETUA'][] = $record;
                    } elseif (str_contains($jabatanName, 'SEKRETARIS')) {
                        $bagan['SEKRETARIS'][] = $record;
                    } elseif (str_contains($jabatanName, 'BENDAHARA')) {
                        $bagan['BENDAHARA'][] = $record;
                    } else {
                        $bagan['ANGGOTA'][] = $record;
                    }
                }
                $renderBlock = function($title, $items, $onlyOrange = false) {
                    ob_start();
                    ?>
                    <div class="flex flex-col w-72 shadow-lg mx-auto rounded-xl overflow-hidden transform hover:-translate-y-1 transition duration-300">
                        <?php if(!$onlyOrange): ?>
                        <div class="bg-gradient-to-r from-green-700 to-green-600 text-white font-bold py-3 px-4 text-center text-sm uppercase tracking-wide">
                            <?= $title ?>
                        </div>
                        <?php endif; ?>
                        <div class="bg-gradient-to-r from-orange-500 to-orange-400 text-white font-bold py-4 px-4 text-center text-sm flex flex-col gap-2 min-h-[3.5rem] justify-center">
                            <?php if(count($items) > 0): ?>
                                <?php foreach($items as $item): ?>
                                    <div class="flex items-center justify-center px-2 py-1 bg-white/10 rounded backdrop-blur-sm">
                                        <div class="text-center drop-shadow-md">
                                            <?php 
                                                if($title === 'PENANGGUNG JAWAB' && str_contains(strtoupper($item->jabatan->name ?? ''), 'KELIAN')) {
                                                    echo "KELIAN ADAT";
                                                } else {
                                                    echo $item->user->name ?? 'Belum ada Pejabat';
                                                }
                                            ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <?php if($title === 'ANGGOTA'): ?>
                                    <div class="text-lg drop-shadow-md">ANGGOTA</div>
                                <?php elseif($title === 'PENANGGUNG JAWAB'): ?>
                                    <div class="text-lg drop-shadow-md">KELIAN ADAT</div>
                                <?php else: ?>
                                    <div class="text-lg drop-shadow-md">-</div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php
                    return ob_get_clean();
                };
            @endphp

            <div class="w-full bg-gray-50 p-6 md:p-12 overflow-x-auto rounded-2xl shadow-inner mt-4 border border-gray-100">
                <div class="min-w-[800px] flex flex-col items-center gap-12 pt-8 pb-12">
                    {{-- Row 1: Penanggung Jawab --}}
                    <div>
                        {!! $renderBlock('PENANGGUNG JAWAB', $bagan['PENANGGUNG JAWAB']) !!}
                    </div>

                    {{-- Row 2: Penasihat --}}
                    <div>
                        {!! $renderBlock('PENASIHAT', $bagan['PENASIHAT']) !!}
                    </div>

                    {{-- Row 3: Ketua --}}
                    <div>
                        {!! $renderBlock('KETUA', $bagan['KETUA']) !!}
                    </div>

                    {{-- Row 4: Sekretaris dan Bendahara --}}
                    <div class="flex flex-row justify-center gap-32 md:gap-48 w-full">
                        <div>
                            {!! $renderBlock('SEKRETARIS', $bagan['SEKRETARIS']) !!}
                        </div>
                        <div>
                            {!! $renderBlock('BENDAHARA', $bagan['BENDAHARA']) !!}
                        </div>
                    </div>

                    {{-- Row 5: Anggota --}}
                    <div class="mt-4">
                        {!! $renderBlock('ANGGOTA', $bagan['ANGGOTA'], true) !!}
                    </div>
                </div>
            </div>
            
        </div>
    </section>

    {{-- Pengumuman Section --}}
    <section class="bg-gray-100">
        <div class="max-w-7xl mx-auto px-4 py-20 space-y-10">
            <div class="text-center md:text-left">
                <h2 class="text-3xl font-extrabold text-gray-900">Pengumuman Resmi</h2>
                <p class="mt-2 text-lg text-gray-600">Pusat informasi dan dokumen resmi organisasi Paiketan Warga Dura Desa.</p>
            </div>
            
            @if($pengumumans->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mt-8">
                    @foreach($pengumumans as $pengumuman)
                        <article class="bg-white rounded-xl border border-gray-200 shadow-sm hover:shadow-lg transition duration-300 overflow-hidden flex flex-col">
                            <div class="p-6 flex-1 flex flex-col">
                                <header class="flex justify-between items-start mb-4">
                                    <h3 class="text-xl font-bold text-gray-900 leading-tight">{{ $pengumuman->judul }}</h3>
                                </header>
                                <div class="mb-4">
                                    <span class="inline-block bg-gray-100 text-gray-600 text-xs px-3 py-1 rounded-full font-medium">
                                        {{ $pengumuman->created_at->translatedFormat('d F Y') }}
                                    </span>
                                </div>
                                
                                @if($pengumuman->keterangan)
                                    <div class="text-gray-600 mb-6 flex-1 text-sm">
                                        {{ $pengumuman->keterangan }}
                                    </div>
                                @endif

                                @if($pengumuman->file)
                                    <div class="mt-auto pt-4 border-t border-gray-100">
                                        <a href="{{ route('pengumuman.file', basename($pengumuman->file)) }}" target="_blank" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium text-sm transition group">
                                            Lihat Dokumen
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6H5.25A2.25 2.25 0 003 8.25v10.5A2.25 2.25 0 005.25 21h10.5A2.25 2.25 0 0018 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                                            </svg>
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </article>
                    @endforeach
                </div>
            @else
                <div class="text-center py-16 px-6 bg-gray-50 rounded-2xl border border-dashed border-gray-300 mt-8">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <p class="text-gray-500 font-medium">Belum ada pengumuman yang dipublikasikan saat ini.</p>
                </div>
            @endif
        </div>
    </section>

    {{-- Footer --}}
    <footer class="bg-gray-900 py-8">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400 text-sm">
                &copy; {{ date('Y') }} Paiketan Warga Dura Desa. All rights reserved.
            </p>
        </div>
    </footer>

</body>
</html>
