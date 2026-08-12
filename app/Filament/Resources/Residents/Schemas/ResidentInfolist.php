<?php

namespace App\Filament\Resources\Residents\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\View;
use Filament\Schemas\Schema;

class ResidentInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                // =====================================================
                // KARTU IDENTITAS (multi-tab: Padukuhan / Akta / Keluarga)
                // Semua data dari ResidentForm sudah dipetakan ke salah
                // satu kartu di dalam view ini.
                // =====================================================

                View::make('filament.infolists.resident-id-card')
                    ->columnSpanFull(),

                // =====================================================
                // PENDIDIKAN & AKUN (belum tercakup di kartu manapun)
                // =====================================================

                Section::make('Akun')
                    ->description('Informasi tambahan yang tidak tercantum pada kartu identitas.')
                    ->icon('heroicon-o-academic-cap')
                    ->iconColor('gray')
                    ->collapsible()
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Akun Login Terhubung')
                            ->icon('heroicon-o-user-circle')
                            ->iconColor('gray')
                            ->placeholder('Belum ada akun login')
                            ->columnSpanFull(),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                // =====================================================
                // RIWAYAT STATUS (hanya tampil jika relevan)
                // =====================================================

                Section::make('Riwayat Status Kependudukan')
                    ->icon('heroicon-o-clipboard-document-list')
                    ->iconColor('gray')
                    ->collapsed()
                    ->visible(fn($record) => $record->status !== 'Aktif' || filled($record->status_note))
                    ->schema([

                        TextEntry::make('status_date')
                            ->label('Tanggal Status')
                            ->date('d M Y')
                            ->placeholder('-')
                            ->columnSpanFull(),

                        TextEntry::make('status_note')
                            ->label('Keterangan')
                            ->placeholder('Belum diisi')
                            ->columnSpanFull(),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

                // =====================================================
                // METADATA SISTEM
                // =====================================================

                Section::make('Informasi Sistem')
                    ->icon('heroicon-o-clock')
                    ->iconColor('gray')
                    ->collapsed()
                    ->schema([

                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->icon('heroicon-o-calendar')
                            ->iconColor('gray')
                            ->since()
                            ->tooltip(fn($record) => $record->created_at->format('d M Y, H:i'))
                            ->columnSpanFull(),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui pada')
                            ->icon('heroicon-o-arrow-path')
                            ->iconColor('gray')
                            ->since()
                            ->tooltip(fn($record) => $record->updated_at->format('d M Y, H:i'))
                            ->columnSpanFull(),

                    ])
                    ->columns(1)
                    ->columnSpanFull(),

            ]);
    }
}
