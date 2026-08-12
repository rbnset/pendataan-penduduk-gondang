<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Widgets\Widget;

class AgeDistributionWidget extends Widget
{
    protected string $view = 'filament.widgets.age-distribution-widget';

    protected int | string | array $columnSpan = 'full';

    public ?int $rwId = null;
    public ?int $rtId = null;

    // Kelompok umur berdasarkan klasifikasi Depkes RI (Kemenkes) 2009 — standar
    // yang umum dipakai untuk data kependudukan/posyandu di Indonesia. Ganti di
    // sini saja kalau instansi/desa kamu pakai standar lain (mis. UU No. 13/1998
    // yang mendefinisikan lansia sebagai 60 tahun ke atas, atau kategori usia
    // produktif BPS 15-64 tahun).
    protected array $ageBrackets = [
        'Balita' => [0, 5, '#f59e0b'],
        'Anak-anak' => [6, 11, '#22c55e'],
        'Remaja Awal' => [12, 16, '#14b8a6'],
        'Remaja Akhir' => [17, 25, '#3b82f6'],
        'Dewasa Awal' => [26, 35, '#6366f1'],
        'Dewasa Akhir' => [36, 45, '#a855f7'],
        'Lansia Awal' => [46, 55, '#ec4899'],
        'Lansia Akhir' => [56, 65, '#ef4444'],
        'Manula' => [66, null, '#6b7280'],
    ];

    public function updatedRwId(): void
    {
        $this->rtId = null;
    }

    protected function baseQuery()
    {
        return Resident::query()
            ->with(['household.rt.rw'])
            ->whereNotNull('birth_date')
            ->when($this->rtId, function ($query) {
                $query->whereHas('household', fn($q) => $q->where('rt_id', $this->rtId));
            })
            ->when($this->rwId && ! $this->rtId, function ($query) {
                $query->whereHas('household.rt', fn($q) => $q->where('rw_id', $this->rwId));
            });
    }

    protected function bracketFor(?int $age): ?string
    {
        if ($age === null) {
            return null;
        }

        foreach ($this->ageBrackets as $label => $range) {
            [$min, $max] = $range;

            if ($age >= $min && ($max === null || $age <= $max)) {
                return $label;
            }
        }

        return null;
    }

    protected function getViewData(): array
    {
        $residents = $this->baseQuery()->get();
        $total = $residents->count();

        $counts = [];
        foreach (array_keys($this->ageBrackets) as $label) {
            $counts[$label] = ['Laki-laki' => 0, 'Perempuan' => 0];
        }

        foreach ($residents as $resident) {
            $label = $this->bracketFor($resident->age);

            if ($label === null) {
                continue;
            }

            $genderKey = $resident->gender === 'Perempuan' ? 'Perempuan' : 'Laki-laki';
            $counts[$label][$genderKey]++;
        }

        $categories = array_keys($this->ageBrackets);
        $maleSeries = [];
        $femaleSeries = [];
        $lifeStages = [];

        foreach ($categories as $label) {
            $male = $counts[$label]['Laki-laki'];
            $female = $counts[$label]['Perempuan'];
            $groupTotal = $male + $female;

            $maleSeries[] = $male;
            $femaleSeries[] = $female;

            [$min, $max, $color] = $this->ageBrackets[$label];
            $rangeLabel = $max === null ? "{$min}+ thn" : "{$min}-{$max} thn";

            $lifeStages[] = [
                'label' => $label,
                'range' => $rangeLabel,
                'color' => $color,
                'count' => $groupTotal,
                'percent' => $total ? round(($groupTotal / $total) * 100, 1) : 0,
            ];
        }

        return [
            'categories' => $categories,
            'maleSeries' => $maleSeries,
            'femaleSeries' => $femaleSeries,
            'lifeStages' => $lifeStages,
            'total' => $total,
            'rws' => Rw::orderBy('number')->get(),
            'rts' => Rt::when($this->rwId, fn($q) => $q->where('rw_id', $this->rwId))
                ->orderBy('number')
                ->get(),
        ];
    }
}
