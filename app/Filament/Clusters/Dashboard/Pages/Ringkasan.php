<?php

namespace App\Filament\Clusters\Dashboard\Pages;

use App\Filament\Clusters\Dashboard\DashboardCluster;
use App\Filament\Widgets\RingkasanDistribusiWidget;
use App\Filament\Widgets\RingkasanStatsWidget;
use Filament\Pages\Page;

class Ringkasan extends Page
{
    protected string $view = 'filament.clusters.dashboard.pages.ringkasan';

    protected static ?string $cluster = DashboardCluster::class;

    protected static ?string $navigationLabel = 'Ringkasan';

    protected static ?string $title = 'Dashboard Ringkasan';

    protected function getHeaderWidgets(): array
    {
        return [
            RingkasanStatsWidget::class,
            RingkasanDistribusiWidget::class,
        ];
    }
}
