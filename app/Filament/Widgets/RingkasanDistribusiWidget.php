<?php

namespace App\Filament\Widgets;

use App\Models\Household;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Widgets\Widget;

class RingkasanDistribusiWidget extends Widget
{
    protected string $view = 'filament.widgets.ringkasan-distribusi-widget';

    protected int | string | array $columnSpan = 'full';

    public ?string $rwId = null;

    public ?string $rtId = null;

    /**
     * Dipanggil otomatis oleh Livewire setiap kali $rwId berubah
     * (karena wire:model.live="rwId" di blade).
     */
    public function updatedRwId(): void
    {
        $this->rtId = null;
    }

    public function rwOptions()
    {
        return Rw::orderBy('number')->get();
    }

    public function rtOptions()
    {
        return Rt::query()
            ->when($this->rwId, fn($query) => $query->where('rw_id', $this->rwId))
            ->orderBy('number')
            ->get();
    }

    /**
     * Query dasar warga sesuai filter RW/RT yang sedang aktif.
     */
    protected function filteredResidentQuery()
    {
        return Resident::query()
            ->when(
                $this->rtId,
                fn($query) => $query->whereHas(
                    'household',
                    fn($q) => $q->where('rt_id', $this->rtId)
                )
            )
            ->when(
                ! $this->rtId && $this->rwId,
                fn($query) => $query->whereHas(
                    'household.rt',
                    fn($q) => $q->where('rw_id', $this->rwId)
                )
            );
    }

    /**
     * Data untuk pie chart sebaran jenis kelamin.
     * Format: [['Laki-laki', 12], ['Perempuan', 15]]
     */
    public function genderChartData(): array
    {
        $base = $this->filteredResidentQuery();

        $male = (clone $base)->where('gender', 'Laki-laki')->count();
        $female = (clone $base)->where('gender', 'Perempuan')->count();

        return [
            ['Laki-laki', $male],
            ['Perempuan', $female],
        ];
    }

    /**
     * Data breakdown per RT: label, jumlah KK, jumlah warga, rata-rata anggota/KK.
     */
    public function rtBreakdownData(): array
    {
        $rts = Rt::query()
            ->when($this->rtId, fn($query) => $query->where('id', $this->rtId))
            ->when(
                ! $this->rtId && $this->rwId,
                fn($query) => $query->where('rw_id', $this->rwId)
            )
            ->with('rw')
            ->orderBy('rw_id')
            ->orderBy('number')
            ->get();

        return $rts->map(function (Rt $rt) {
            $kkCount = Household::where('rt_id', $rt->id)->count();

            $wargaCount = Resident::whereHas(
                'household',
                fn($query) => $query->where('rt_id', $rt->id)
            )->count();

            $rataAnggota = $kkCount > 0 ? round($wargaCount / $kkCount, 1) : 0;

            $rwNumber = $rt->rw?->number ?? '?';

            return [
                "RW {$rwNumber} / RT {$rt->number}",
                $kkCount,
                $wargaCount,
                $rataAnggota,
            ];
        })->toArray();
    }
}
