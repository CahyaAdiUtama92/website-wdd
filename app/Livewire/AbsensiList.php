<?php

namespace App\Livewire;

use App\Models\Absensi;
use Livewire\Component;
use Livewire\WithPagination;

class AbsensiList extends Component
{
    use WithPagination;

    public int $rapatId;
    public bool $absensiDisabled = true;

    /**
     * Reset pagination setiap kali rapatId berubah
     */
    public function updatedRapatId(): void
    {
        $this->resetPage();
    }

    /**
     * Simpan perubahan status kehadiran satu anggota
     */
    public function updateStatus(int $absensiId, string $status): void
    {
        if ($this->absensiDisabled) {
            return;
        }

        Absensi::where('id', $absensiId)
            ->where('rapat_id', $this->rapatId)
            ->update(['status' => $status]);
    }

    /**
     * Simpan perubahan keterangan satu anggota
     */
    public function updateKeterangan(int $absensiId, string $keterangan): void
    {
        if ($this->absensiDisabled) {
            return;
        }

        Absensi::where('id', $absensiId)
            ->where('rapat_id', $this->rapatId)
            ->update(['keterangan' => $keterangan]);
    }

    public function render()
    {
        $absensis = Absensi::with('user')
            ->where('rapat_id', $this->rapatId)
            ->orderBy(
                \App\Models\User::select('name')
                    ->whereColumn('users.id', 'absensis.user_id')
                    ->limit(1)
            )
            ->paginate(10);

        return view('livewire.absensi-list', [
            'absensis'        => $absensis,
            'absensiDisabled' => $this->absensiDisabled,
        ]);
    }
}
