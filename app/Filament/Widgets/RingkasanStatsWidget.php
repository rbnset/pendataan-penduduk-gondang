<?php

namespace App\Filament\Widgets;

use App\Models\Household;
use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class RingkasanStatsWidget extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalWarga = Resident::count();
        $totalKk = Household::count();
        $totalRt = Rt::count();
        $totalRw = Rw::count();

        $eligibleKtp = Resident::whereDate('birth_date', '<=', now()->subYears(17))->count();
        $ktpCount = Resident::where('has_ktp', true)->count();
        $ktpPercent = $eligibleKtp > 0 ? round(($ktpCount / $eligibleKtp) * 100, 1) : 0;

        return [
            Stat::make('Total Warga', number_format($totalWarga, 0, ',', '.'))
                ->icon('heroicon-o-users'),

            Stat::make('Total Kepala Keluarga', number_format($totalKk, 0, ',', '.'))
                ->icon('heroicon-o-home'),

            Stat::make('Total RT', $totalRt)
                ->icon('heroicon-o-map'),

            Stat::make('Total RW', $totalRw)
                ->icon('heroicon-o-map-pin'),

            Stat::make('Kepemilikan KTP', "{$ktpPercent}%")
                ->description("{$ktpCount} dari {$eligibleKtp} warga usia ≥17 tahun")
                ->icon('heroicon-o-identification')
                ->color($ktpPercent >= 80 ? 'success' : ($ktpPercent >= 50 ? 'warning' : 'danger')),
        ];
    }
}
