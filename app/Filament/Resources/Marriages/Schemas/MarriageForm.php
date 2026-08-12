<?php

namespace App\Filament\Resources\Marriages\Schemas;

use App\Models\Resident;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;

class MarriageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =========================================================
                // DATA PASANGAN
                // =========================================================
                Section::make('Data Pasangan')
                    ->description('Pilih warga yang tercatat sebagai suami dan istri.')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                Select::make('husband_resident_id')
                                    ->label('Suami')
                                    ->relationship(
                                        name: 'husband',
                                        titleAttribute: 'full_name',
                                        modifyQueryUsing: fn($query) => $query->where('gender', 'Laki-laki'),
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn(Resident $record) => "{$record->full_name} — {$record->nik}"
                                    )
                                    ->searchable(['full_name', 'nik'])
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Pilih warga (suami)')
                                    ->required()
                                    ->distinct(),

                                Select::make('wife_resident_id')
                                    ->label('Istri')
                                    ->relationship(
                                        name: 'wife',
                                        titleAttribute: 'full_name',
                                        modifyQueryUsing: fn($query) => $query->where('gender', 'Perempuan'),
                                    )
                                    ->getOptionLabelFromRecordUsing(
                                        fn(Resident $record) => "{$record->full_name} — {$record->nik}"
                                    )
                                    ->searchable(['full_name', 'nik'])
                                    ->preload()
                                    ->native(false)
                                    ->placeholder('Pilih warga (istri)')
                                    ->required()
                                    ->distinct(),

                            ]),
                    ]),

                // =========================================================
                // DATA AKTA NIKAH
                // =========================================================
                Section::make('Data Akta Nikah')
                    ->description('Informasi pencatatan pernikahan secara resmi.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        Grid::make(2)
                            ->schema([

                                TextInput::make('marriage_certificate_number')
                                    ->label('Nomor Akta Nikah')
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->placeholder('Contoh: 1234/NK/2020')
                                    ->required()
                                    ->maxLength(50)
                                    ->columnSpanFull(),

                                DatePicker::make('marriage_date')
                                    ->label('Tanggal Nikah')
                                    ->placeholder('Pilih tanggal nikah')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->required(),

                                TextInput::make('kua_name')
                                    ->label('KUA Pencatat')
                                    ->prefixIcon('heroicon-o-building-library')
                                    ->placeholder('Contoh: KUA Kecamatan Banyudono')
                                    ->maxLength(100),

                            ]),
                    ]),

                // =========================================================
                // STATUS PERCERAIAN (opsional)
                // =========================================================
                Section::make('Status Perceraian')
                    ->description('Aktifkan hanya jika pernikahan telah berakhir dengan perceraian.')
                    ->icon('heroicon-o-scale')
                    ->schema([

                        Toggle::make('is_divorced')
                            ->label('Pernikahan Telah Bercerai')
                            ->live()
                            ->dehydrated(false)
                            ->afterStateHydrated(function (Toggle $component, $record) {
                                $component->state($record && $record->divorce_date !== null);
                            })
                            ->columnSpanFull(),

                        Grid::make(2)
                            ->schema([

                                TextInput::make('divorce_certificate_number')
                                    ->label('Nomor Akta Cerai')
                                    ->prefixIcon('heroicon-o-hashtag')
                                    ->placeholder('Contoh: 5678/CR/2023')
                                    ->maxLength(50)
                                    ->visible(fn(Get $get) => $get('is_divorced'))
                                    ->required(fn(Get $get) => $get('is_divorced')),

                                DatePicker::make('divorce_date')
                                    ->label('Tanggal Cerai')
                                    ->placeholder('Pilih tanggal cerai')
                                    ->displayFormat('d/m/Y')
                                    ->native(false)
                                    ->visible(fn(Get $get) => $get('is_divorced'))
                                    ->required(fn(Get $get) => $get('is_divorced'))
                                    ->afterOrEqual('marriage_date'),
                            ]),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

            ])
            ->columns(1);
    }
}
