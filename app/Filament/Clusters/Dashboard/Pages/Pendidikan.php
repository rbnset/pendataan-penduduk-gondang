<?php

namespace App\Filament\Clusters\Dashboard\Pages;

use App\Filament\Clusters\Dashboard\DashboardCluster;
use App\Filament\Widgets\EducationDistributionWidget;
use Filament\Pages\Page;

class Pendidikan extends Page
{
    protected string $view = 'filament.clusters.dashboard.pages.pendidikan';

    protected static ?string $cluster = DashboardCluster::class;

    protected static ?string $navigationLabel = 'Pendidikan';

    protected static ?string $title = 'Dashboard Pendidikan';

    protected function getHeaderWidgets(): array
    {
        return [
            EducationDistributionWidget::class,
        ];
    }
}
