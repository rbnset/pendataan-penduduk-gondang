<?php

namespace App\Filament\Resources\Households\Schemas;

use App\Models\Household;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class HouseholdInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =====================================================
                // RINGKASAN CEPAT
                // =====================================================

                Section::make('Informasi Kartu Keluarga')
                    ->description('Informasi utama kartu keluarga')
                    ->icon('heroicon-o-document-text')
                    ->iconColor('primary')
                    ->schema([

                        Grid::make(['default' => 1, 'md' => 4])
                            ->columns(2)
                            ->schema([

                                TextEntry::make('no_kk')
                                    ->label('Nomor KK')
                                    ->icon('heroicon-o-identification')
                                    ->iconColor('gray')
                                    ->weight('bold')
                                    ->size('lg')
                                    ->copyable()
                                    ->copyMessage('Nomor KK disalin')
                                    ->columnSpan(['default' => 1, 'md' => 2]),

                                TextEntry::make('head.full_name')
                                    ->label('Kepala Keluarga')
                                    ->icon('heroicon-o-user-circle')
                                    ->iconColor(fn(Household $record) => $record->head ? 'success' : 'danger')
                                    ->weight('semibold')
                                    ->placeholder('Belum ditentukan')
                                    ->color(fn(Household $record) => $record->head ? null : 'danger')
                                    ->columnSpan(1),

                                TextEntry::make('residents_count')
                                    ->label('Jumlah Anggota')
                                    ->icon('heroicon-o-users')
                                    ->iconColor('info')
                                    ->state(
                                        fn(Household $record) => $record->residents()->count()
                                    )
                                    ->formatStateUsing(fn($state) => "{$state} orang")
                                    ->badge()
                                    ->color(fn($state) => match (true) {
                                        $state === 0 => 'gray',
                                        $state < 3 => 'warning',
                                        default => 'success',
                                    })
                                    ->columnSpan(1),

                            ]),

                    ]),

                // =====================================================
                // INFORMASI KARTU KELUARGA
                // =====================================================

                Section::make('Wilayah & Alamat')
                    ->description('Lokasi dan domisili kartu keluarga')
                    ->icon('heroicon-o-map-pin')
                    ->iconColor('primary')
                    ->columns(['default' => 1, 'md' => 2])
                    ->schema([

                        TextEntry::make('rt.number')
                            ->label('RT / RW')
                            ->icon('heroicon-o-building-office-2')
                            ->badge()
                            ->color('primary')
                            ->placeholder('-')
                            ->formatStateUsing(
                                fn($state, Household $record) => $record->rt
                                    ? "RT {$record->rt->number} / RW {$record->rt->rw->number}"
                                    : 'Belum ditentukan'
                            ),

                        TextEntry::make('pln_customer_number')
                            ->label('ID Pelanggan PLN')
                            ->icon('heroicon-o-bolt')
                            ->iconColor('warning')
                            ->placeholder('Belum diisi')
                            ->copyable()
                            ->copyMessage('ID Pelanggan PLN disalin'),

                        TextEntry::make('address')
                            ->label('Alamat Lengkap')
                            ->icon('heroicon-o-home')
                            ->iconColor('gray')
                            ->placeholder('Belum diisi')
                            ->columnSpanFull(),

                    ]),

                // =====================================================
                // METADATA SISTEM
                // =====================================================

                Section::make('Informasi Sistem')
                    ->columnSpanFull()
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->columns(['default' => 1, 'md' => 2])
                    ->collapsed()
                    ->schema([

                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('gray')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->tooltip(fn(Household $record) => $record->created_at->format('d M Y, H:i')),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui pada')
                            ->icon('heroicon-o-arrow-path')
                            ->iconColor('gray')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->tooltip(fn(Household $record) => $record->updated_at->format('d M Y, H:i')),

                    ]),

            ]);
    }
}
