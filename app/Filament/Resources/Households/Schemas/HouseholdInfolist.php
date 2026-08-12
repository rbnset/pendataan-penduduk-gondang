<?php

namespace App\Filament\Resources\Households\Schemas;

use App\Models\Household;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class HouseholdInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =====================================================
                // KARTU KELUARGA (hero ID card + daftar anggota)
                // Mencakup: no_kk, kepala keluarga, RT/RW, alamat,
                // ID pelanggan PLN, dan seluruh anggota keluarga.
                // =====================================================

                View::make('filament.infolists.household-id-card')
                    ->columnSpanFull(),

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
