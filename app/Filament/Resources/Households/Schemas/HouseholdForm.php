<?php

namespace App\Filament\Resources\Households\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class HouseholdForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('rt_id')
                    ->relationship('rt', 'number')
                    ->label('Wilayah RT')
                    ->getOptionLabelFromRecordUsing(
                        fn ($record) => 'RT ' . $record->number . ' / RW ' . $record->rw->number
                    )
                    ->searchable()
                    ->preload()
                    ->required(),

                TextInput::make('no_kk')
                    ->label('Nomor KK')
                    ->required()
                    ->maxLength(20),

                TextInput::make('pln_customer_number')
                    ->label('ID Pelanggan PLN')
                    ->maxLength(20)
                    ->nullable(),

                Textarea::make('address')
                    ->label('Alamat')
                    ->required()
                    ->rows(3)
                    ->columnSpanFull(),
            ]);
    }
}