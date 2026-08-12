<?php

namespace App\Filament\Widgets;

use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Widgets\Widget;

class MaritalStatusWidget extends Widget
{
    protected string $view = 'filament.widgets.marital-status-widget';

    protected int | string | array $columnSpan = 'full';

    public ?int $rwId = null;
    public ?int $rtId = null;
    public ?string $statusFilter = null;
    public ?int $year = null;

    protected array $colorPalette = [
        '#22c55e',
        '#3b82f6',
        '#f59e0b',
        '#ef4444',
        '#a855f7',
        '#14b8a6',
    ];

    public function updatedRwId(): void
    {
        $this->rtId = null;
    }

    protected function baseQuery()
    {
        return Resident::query()
            ->with(['household.rt.rw'])
            ->when($this->rtId, function ($query) {
                $query->whereHas('household', fn($q) => $q->where('rt_id', $this->rtId));
            })
            ->when($this->rwId && ! $this->rtId, function ($query) {
                $query->whereHas('household.rt', fn($q) => $q->where('rw_id', $this->rwId));
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('marital_status', $this->statusFilter);
            })
            ->when($this->year, function ($query) {
                $query->whereYear('status_date', $this->year);
            });
    }

    protected function getViewData(): array
    {
        $residents = $this->baseQuery()->get();
        $total = $residents->count();

        $groupedByStatus = $residents->groupBy(fn($r) => $r->marital_status ?: 'Belum Diisi');

        $statusData = [];
        $detailData = [];
        $detailGrouped = [];
        $colorIndex = 0;

        foreach ($groupedByStatus as $statusName => $group) {
            $baseColor = $this->colorPalette[$colorIndex % count($this->colorPalette)];

            $statusData[] = [
                'name' => $statusName,
                'y' => $group->count(),
                'color' => $baseColor,
            ];

            $byLocation = $group->groupBy(function ($r) {
                $rw = $r->household?->rt?->rw?->number ?? '-';
                $rt = $r->household?->rt?->number ?? '-';

                return "RW {$rw} / RT {$rt}";
            });

            $locCount = max($byLocation->count(), 1);
            $i = 0;
            $detailGrouped[$statusName] = [];

            foreach ($byLocation as $locName => $locGroup) {
                $detailData[] = [
                    'name' => $locName,
                    'y' => $locGroup->count(),
                    'color' => $baseColor,
                    'brightness' => round(0.25 - ($i / $locCount) / 4, 2),
                ];

                $detailGrouped[$statusName][] = [
                    'name' => $locName,
                    'y' => $locGroup->count(),
                ];

                $i++;
            }

            $colorIndex++;
        }

        return [
            'statusData' => $statusData,
            'detailData' => $detailData,
            'detailByStatusGrouped' => $detailGrouped,
            'total' => $total,
            'rws' => Rw::orderBy('number')->get(),
            'rts' => Rt::when($this->rwId, fn($q) => $q->where('rw_id', $this->rwId))
                ->orderBy('number')
                ->get(),
            'statuses' => Resident::whereNotNull('marital_status')
                ->distinct()
                ->orderBy('marital_status')
                ->pluck('marital_status'),
            'years' => Resident::whereNotNull('status_date')
                ->selectRaw('DISTINCT YEAR(status_date) as year')
                ->orderByDesc('year')
                ->pluck('year'),
        ];
    }
}
