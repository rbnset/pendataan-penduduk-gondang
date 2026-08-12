<?php

namespace App\Filament\Clusters\Dashboard\Pages;

use App\Filament\Clusters\Dashboard\DashboardCluster;
use App\Filament\Widgets\AgeDistributionWidget;
use Filament\Pages\Page;

class Usia extends Page
{
    protected string $view = 'filament.clusters.dashboard.pages.usia';

    protected static ?string $cluster = DashboardCluster::class;

    protected static ?string $navigationLabel = 'Usia';

    protected static ?string $title = 'Dashboard Usia';

    protected function getHeaderWidgets(): array
    {
        return [
            AgeDistributionWidget::class,
        ];
    }
}
