@php
    $bagan = [
        'PENANGGUNG JAWAB' => [],
        'PENASIHAT' => [],
        'KETUA' => [],
        'SEKRETARIS' => [],
        'BENDAHARA' => [],
        'ANGGOTA' => [],
    ];
    foreach($records as $record) {
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
        <div class="flex flex-col w-72 shadow-lg mx-auto rounded-sm overflow-hidden">
            <?php if(!$onlyOrange): ?>
            <div class="bg-[#2a8e57] text-white font-bold py-3 px-4 text-center text-sm uppercase tracking-wide">
                <?= $title ?>
            </div>
            <?php endif; ?>
            <div class="bg-[#e67e22] text-white font-bold py-4 px-4 text-center text-sm flex flex-col gap-2 min-h-[3.5rem] justify-center">
                <?php if(count($items) > 0): ?>
                    <?php foreach($items as $item): ?>
                        <div class="flex items-center justify-between group px-2 py-1 hover:bg-orange-600 rounded transition-colors">
                            <div class="text-center flex-1">
                                <?php 
                                    // Jika jabatan Kelian Adat, tampilkan teks khusus atau nama user
                                    if($title === 'PENANGGUNG JAWAB' && str_contains(strtoupper($item->jabatan->name ?? ''), 'KELIAN')) {
                                        echo "KELIAN ADAT";
                                    } else {
                                        echo $item->user->name ?? 'Belum ada Pejabat';
                                    }
                                ?>
                            </div>
                            <?php if(auth()->check() && (auth()->user()->is_super_admin || auth()->user()->role?->name === 'Ketua')): ?>
                            <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button wire:click="mountTableAction('edit', '<?= $item->getKey() ?>')" type="button" class="text-white hover:text-gray-200" title="Edit">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                </button>
                                <button wire:click="mountTableAction('delete', '<?= $item->getKey() ?>')" type="button" class="text-red-200 hover:text-white" title="Hapus">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>
                            </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?php if($title === 'ANGGOTA'): ?>
                        <div class="text-lg">ANGGOTA</div>
                    <?php elseif($title === 'PENANGGUNG JAWAB'): ?>
                        <div class="text-lg">KELIAN ADAT</div>
                    <?php else: ?>
                        <div class="text-lg">-</div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
        <?php
        return ob_get_clean();
    };
@endphp

<div class="w-full bg-white p-12 min-h-[800px] overflow-x-auto rounded-xl shadow-inner mt-4">
    <div class="min-w-[800px] flex flex-col items-center gap-16 pt-8 pb-16">
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
        <div class="flex flex-row justify-center gap-48 w-full">
            <div>
                {!! $renderBlock('SEKRETARIS', $bagan['SEKRETARIS']) !!}
            </div>
            <div>
                {!! $renderBlock('BENDAHARA', $bagan['BENDAHARA']) !!}
            </div>
        </div>

        {{-- Row 5: Anggota --}}
        <div>
            {!! $renderBlock('ANGGOTA', $bagan['ANGGOTA'], true) !!}
        </div>
    </div>
</div>