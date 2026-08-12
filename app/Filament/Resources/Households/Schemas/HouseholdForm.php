<?php

namespace App\Filament\Resources\Households\Schemas;

use App\Models\Rt;
use App\Models\Rw;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HouseholdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Wilayah')
                    ->description('Pilih RW terlebih dahulu, lalu RT akan menyesuaikan')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                Select::make('rw_id')
                                    ->label('Wilayah RW')
                                    ->required()
                                    ->options(
                                        fn() => Rw::query()
                                            ->orderBy('number')
                                            ->get()
                                            ->mapWithKeys(fn($rw) => [$rw->id => 'RW ' . $rw->number])
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Pilih RW')
                                    ->live()
                                    ->dehydrated(false) // tidak disimpan ke DB, hanya filter
                                    ->afterStateUpdated(fn(callable $set) => $set('rt_id', null))
                                    // Saat edit, isi otomatis dari relasi rt->rw
                                    ->afterStateHydrated(function (callable $set, $record) {
                                        if ($record?->rt?->rw_id) {
                                            $set('rw_id', $record->rt->rw_id);
                                        }
                                    }),

                                Select::make('rt_id')
                                    ->relationship('rt', 'number')
                                    ->label('Wilayah RT')
                                    ->required()
                                    ->getOptionLabelFromRecordUsing(
                                        fn($record) => 'RT ' . $record->number
                                    )
                                    ->options(function (callable $get) {
                                        $rwId = $get('rw_id');

                                        if (! $rwId) {
                                            return [];
                                        }

                                        return Rt::query()
                                            ->where('rw_id', $rwId)
                                            ->orderBy('number')
                                            ->get()
                                            ->mapWithKeys(fn($rt) => [$rt->id => 'RT ' . $rt->number]);
                                    })
                                    ->searchable()
                                    ->native(false)
                                    ->placeholder('Pilih RW dahulu')
                                    ->disabled(fn(callable $get) => ! $get('rw_id'))
                                    ->required(),
                            ]),
                    ]),

                Section::make('Data Keluarga')
                    ->description('Informasi identitas dan layanan rumah tangga')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('no_kk')
                                    ->label('Nomor KK')
                                    ->prefixIcon('heroicon-o-document-text')
                                    ->placeholder('Contoh: 3372010101010001')
                                    ->required()
                                    ->minLength(16)
                                    ->maxLength(16)
                                    ->rule('digits:16')
                                    ->extraInputAttributes([
                                        'maxlength' => 16,
                                        'inputmode' => 'numeric',
                                        'pattern' => '[0-9]*',
                                    ])
                                    ->live(onBlur: true)
                                    ->helperText('Wajib 16 digit sesuai Kartu Keluarga'),

                                TextInput::make('pln_customer_number')
                                    ->label('ID Pelanggan PLN')
                                    ->prefixIcon('heroicon-o-bolt')
                                    ->placeholder('Opsional')
                                    ->maxLength(20)
                                    ->nullable()
                                    ->helperText('Kosongkan jika belum tersedia'),
                            ]),
                    ]),

                Section::make('Alamat')
                    ->icon('heroicon-o-home')
                    ->schema([
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->placeholder('Nama jalan, nomor rumah, patokan, dll.')
                            ->required()
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ])
            ->columns(1);
    }
}
