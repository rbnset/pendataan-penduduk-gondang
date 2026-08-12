<?php

namespace App\Filament\Resources\Residents\Pages;

use App\Filament\Resources\Residents\ResidentResource;
use App\Filament\Imports\ResidentImporter;
use App\Filament\Resources\Residents\Widgets\ResidentOverview;
use App\Models\Resident;
use App\Models\Rw;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\ImportAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListResidents extends ListRecords
{
    protected static string $resource = ResidentResource::class;

    /**
     * Filter utama berdasarkan RW.
     *
     * RW dibuat sebagai tab karena ini merupakan
     * filter yang paling sering digunakan pada data warga.
     */

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Semua')
                ->badge(
                    fn() => Resident::count()
                ),
        ];

        $rwTabs = Rw::query()
            ->orderBy('number')
            ->get()
            ->mapWithKeys(function (Rw $rw) {

                return [
                    'rw_' . $rw->id => Tab::make(
                        'RW ' . $rw->number
                    )
                        ->badge(
                            fn() => Resident::query()
                                ->whereHas(
                                    'household',
                                    fn(Builder $query) =>
                                    $query->whereHas(
                                        'rt',
                                        fn(Builder $query) =>
                                        $query->where(
                                            'rw_id',
                                            $rw->id
                                        )
                                    )
                                )
                                ->count()
                        )
                        ->modifyQueryUsing(
                            fn(Builder $query) =>
                            $query->whereHas(
                                'household',
                                fn(Builder $query) =>
                                $query->whereHas(
                                    'rt',
                                    fn(Builder $query) =>
                                    $query->where(
                                        'rw_id',
                                        $rw->id
                                    )
                                )
                            )
                        ),
                ];
            })
            ->all();

        return array_merge($tabs, $rwTabs);
    }

    protected function getHeaderActions(): array
    {
        return [

            // =========================================================
            // IMPORT
            // =========================================================

            ImportAction::make()
                ->importer(ResidentImporter::class)
                ->label('Import CSV')
                ->icon('heroicon-o-arrow-up-tray'),

            // =========================================================
            // EXPORT
            // =========================================================

            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(function () {

                    $residents = $this
                        ->getFilteredTableQuery()
                        ->get();

                    $pdf = Pdf::loadView(
                        'reports.residents',
                        [
                            'residents' => $residents,
                        ]
                    )->setPaper('a4', 'landscape');

                    return response()->streamDownload(
                        fn() => print($pdf->output()),
                        'laporan-warga-' .
                            now()->format('Ymd-His') .
                            '.pdf'
                    );
                }),

            // =========================================================
            // TAMBAH WARGA
            // =========================================================

            CreateAction::make()
                ->label('Tambah Warga'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            ResidentOverview::class,
        ];
    }
}
