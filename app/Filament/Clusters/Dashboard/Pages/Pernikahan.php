<?php

namespace App\Filament\Clusters\Dashboard\Pages;

use App\Filament\Clusters\Dashboard\DashboardCluster;
use App\Filament\Widgets\MaritalStatusWidget;
use Filament\Pages\Page;

class Pernikahan extends Page
{
    protected string $view = 'filament.clusters.dashboard.pages.pernikahan';

    protected static ?string $cluster = DashboardCluster::class;

    protected static ?string $navigationLabel = 'Pernikahan';

    protected static ?string $title = 'Dashboard Pernikahan';

    protected function getHeaderWidgets(): array
    {
        return [
            MaritalStatusWidget::class,
        ];
    }
}
