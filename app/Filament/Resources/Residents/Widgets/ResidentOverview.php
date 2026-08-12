<?php

namespace App\Filament\Resources\Residents\Widgets;

use App\Models\Resident;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class ResidentOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        $totalResidents = Resident::query()->count();

        $totalHouseholds = Resident::query()
            ->whereNotNull('household_id')
            ->distinct('household_id')
            ->count('household_id');

        $totalRw = Rw::query()->count();

        $totalRt = Rt::query()->count();

        return [
            Stat::make(
                'Total Warga',
                number_format($totalResidents)
            )
                ->description('Seluruh penduduk terdaftar')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),

            Stat::make(
                'Kartu Keluarga',
                number_format($totalHouseholds)
            )
                ->description('Keluarga terdaftar')
                ->descriptionIcon('heroicon-m-home')
                ->color('success'),

            Stat::make(
                'RW',
                number_format($totalRw)
            )
                ->description('Wilayah RW')
                ->descriptionIcon('heroicon-m-map')
                ->color('info'),

            Stat::make(
                'RT',
                number_format($totalRt)
            )
                ->description('Wilayah RT')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('gray'),
        ];
    }

    protected function getColumns(): int|array
    {
        return 4;
    }
}
