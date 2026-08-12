<?php

namespace App\Filament\Resources\Marriages\Schemas;

use App\Models\Marriage;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class MarriageInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =====================================================
                // BUKU NIKAH (hero ID card)
                // Mencakup: suami, istri, no. akta nikah, tanggal nikah,
                // KUA pencatat, dan info perceraian jika ada.
                // =====================================================

                View::make('filament.infolists.marriage-id-card')
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
                            ->tooltip(fn(Marriage $record) => $record->created_at->format('d M Y, H:i')),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui pada')
                            ->icon('heroicon-o-arrow-path')
                            ->iconColor('gray')
                            ->dateTime('d M Y, H:i')
                            ->since()
                            ->tooltip(fn(Marriage $record) => $record->updated_at->format('d M Y, H:i')),

                    ]),

            ]);
    }
}
