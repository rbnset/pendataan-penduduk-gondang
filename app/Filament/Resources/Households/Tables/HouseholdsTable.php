<?php

namespace App\Filament\Resources\Households\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class HouseholdsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('no_kk')
                    ->label('Nomor KK')
                    ->searchable()
                    ->copyable(),

                TextColumn::make('head.full_name')
                    ->label('Kepala Keluarga')
                    ->default('-')
                    ->searchable(),

                TextColumn::make('rt.number')
                    ->label('RT')
                    ->sortable(),

                TextColumn::make('rt.rw.number')
                    ->label('RW')
                    ->sortable(),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(40)
                    ->searchable(),

                TextColumn::make('residents_count')
                    ->label('Anggota')
                    ->counts('residents')
                    ->sortable(),

                TextColumn::make('pln_customer_number')
                    ->label('ID PLN')
                    ->placeholder('Belum diisi')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->defaultSort('no_kk')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}