<?php

namespace App\Filament\Resources\Rws\Schemas;

use App\Models\Rw;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class RwInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                Section::make('Informasi RW')
                    ->columns(3)
                    ->schema([

                        TextEntry::make('number')
                            ->label('Nomor RW')
                            ->formatStateUsing(fn($state) => "RW {$state}")
                            ->weight('bold')
                            ->size('lg'),

                        TextEntry::make('chairman.full_name')
                            ->label('Ketua RW')
                            ->placeholder('Belum ditentukan')
                            ->columnSpan(2),

                        TextEntry::make('rts_count')
                            ->label('Jumlah RT')
                            ->state(
                                fn(Rw $record) => $record->rts()->count() . ' RT'
                            )
                            ->badge()
                            ->color('info'),

                    ]),

                Section::make('Informasi Sistem')
                    ->columns(2)
                    ->collapsed()
                    ->schema([

                        TextEntry::make('created_at')
                            ->label('Dibuat pada')
                            ->dateTime('d M Y H:i'),

                        TextEntry::make('updated_at')
                            ->label('Diperbarui pada')
                            ->dateTime('d M Y H:i'),

                    ]),

            ]);
    }
}
