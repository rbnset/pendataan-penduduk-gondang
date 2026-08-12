<?php

namespace App\Filament\Imports;

use App\Models\Household;
use App\Models\Rt;
use App\Models\Rw;
use Filament\Actions\Imports\Exceptions\RowImportFailedException;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;
use Illuminate\Support\Number;

class HouseholdImporter extends Importer
{
    protected static ?string $model = Household::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('rw_number')
                ->label('Nomor RW')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:1'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('rt_number')
                ->label('Nomor RT')
                ->requiredMapping()
                ->rules(['required', 'integer', 'min:1'])
                ->fillRecordUsing(fn() => null),

            ImportColumn::make('no_kk')
                ->label('Nomor KK')
                ->requiredMapping()
                ->rules(['required', 'digits:16']),

            ImportColumn::make('address')
                ->label('Alamat')
                ->requiredMapping()
                ->rules(['required']),

            ImportColumn::make('pln_customer_number')
                ->label('ID Pelanggan PLN')
                ->rules(['nullable', 'max:20']),
        ];
    }

    public function resolveRecord(): Household
    {
        // Cast ke integer supaya "99", " 99 ", "099" semua dianggap sama,
        // dan cocok dengan tipe kolom number (integer) di DB.
        $rwNumber = (int) trim((string) $this->data['rw_number']);
        $rtNumber = (int) trim((string) $this->data['rt_number']);
        $noKk = trim((string) $this->data['no_kk']);

        // Auto-create RW kalau belum ada.
        $rw = Rw::firstOrCreate(['number' => $rwNumber]);

        // Auto-create RT (di dalam RW tersebut) kalau belum ada.
        $rt = Rt::firstOrCreate([
            'rw_id' => $rw->id,
            'number' => $rtNumber,
        ]);

        $household = Household::firstOrNew([
            'no_kk' => $noKk,
        ]);

        $household->rt_id = $rt->id;

        return $household;
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = 'Import data kartu keluarga selesai, ' . Number::format($import->successful_rows) . ' baris berhasil diimpor.';

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . Number::format($failedRowsCount) . ' baris gagal diimpor.';
        }

        return $body;
    }
}
