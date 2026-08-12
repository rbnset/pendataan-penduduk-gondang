<?php

namespace App\Filament\Resources\Rws\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class RwForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([

                TextInput::make('number')
                    ->label('Nomor RW')
                    ->placeholder('Contoh: 01')
                    ->required(),

                Select::make('chairman_resident_id')
                    ->relationship('chairman', 'full_name')
                    ->label('Ketua RW')
                    ->placeholder('Pilih ketua RW')
                    ->helperText('Opsional — biasanya baru bisa dipilih setelah warga di wilayah ini terdaftar.')
                    ->searchable()
                    ->preload()
                    ->default(null),

            ]);
    }
}
