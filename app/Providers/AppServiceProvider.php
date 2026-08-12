<?php

namespace App\Providers;

use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        FilamentAsset::register([
            Js::make('highcharts', 'https://code.highcharts.com/highcharts.js'),
            Js::make('highcharts-more', 'https://code.highcharts.com/highcharts-more.js'),
            Js::make('highcharts-dashboards', 'https://code.highcharts.com/dashboards/dashboards.js'),
            Js::make('highcharts-exporting', 'https://code.highcharts.com/modules/exporting.js'),
            Js::make('highcharts-export-data', 'https://code.highcharts.com/modules/export-data.js'),
            Js::make('highcharts-accessibility', 'https://code.highcharts.com/modules/accessibility.js'),
            Css::make('highcharts-dashboards-css', 'https://code.highcharts.com/dashboards/css/dashboards.css'),
        ]);
    }
}
