<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Widgets\Widget;

class EducationDistributionWidget extends Widget
{
    protected string $view = 'filament.widgets.education-distribution-widget';

    protected int | string | array $columnSpan = 'full';

    public ?int $rwId = null;
    public ?int $rtId = null;

    // Urutan jenjang "Pendidikan Terakhir" mengikuti kategori baku yang
    // dipakai Dukcapil/Kemendagri untuk data KTP/KK. Ganti di sini saja kalau
    // nilai kolom `education` di database kamu pakai istilah/urutan lain.
    protected array $educationLevels = [
        'Tidak/Belum Sekolah',
        // 'Belum Tamat SD',
        'SD',
        'SMP',
        'SMA',
        'D3',
        'S1',
        'S2',
        'S3',
    ];

    public function updatedRwId(): void
    {
        $this->rtId = null;
    }

    protected function baseQuery()
    {
        return Resident::query()
            ->with(['household.rt.rw'])
            ->whereNotNull('education')
            ->when($this->rtId, function ($query) {
                $query->whereHas('household', fn($q) => $q->where('rt_id', $this->rtId));
            })
            ->when($this->rwId && ! $this->rtId, function ($query) {
                $query->whereHas('household.rt', fn($q) => $q->where('rw_id', $this->rwId));
            });
    }

    protected function getViewData(): array
    {
        $residents = $this->baseQuery()->get();
        $total = $residents->count();

        $counts = [];
        foreach ($this->educationLevels as $level) {
            $counts[$level] = ['Laki-laki' => 0, 'Perempuan' => 0];
        }

        // Kalau ada nilai `education` di database yang tidak cocok dengan
        // salah satu kategori di atas (mis. typo atau istilah lama), kita
        // tampung supaya kelihatan sebagai catatan, bukan hilang diam-diam.
        $unmapped = 0;

        foreach ($residents as $resident) {
            $level = $resident->education;

            if (! array_key_exists($level, $counts)) {
                $unmapped++;
                continue;
            }

            $genderKey = $resident->gender === 'Perempuan' ? 'Perempuan' : 'Laki-laki';
            $counts[$level][$genderKey]++;
        }

        $categories = $this->educationLevels;
        $maleSeries = [];
        $femaleSeries = [];

        foreach ($categories as $level) {
            $maleSeries[] = $counts[$level]['Laki-laki'];
            $femaleSeries[] = $counts[$level]['Perempuan'];
        }

        return [
            'categories' => $categories,
            'maleSeries' => $maleSeries,
            'femaleSeries' => $femaleSeries,
            'unmappedCount' => $unmapped,
            'total' => $total,
            'rws' => Rw::orderBy('number')->get(),
            'rts' => Rt::when($this->rwId, fn($q) => $q->where('rw_id', $this->rwId))
                ->orderBy('number')
                ->get(),
        ];
    }
}
