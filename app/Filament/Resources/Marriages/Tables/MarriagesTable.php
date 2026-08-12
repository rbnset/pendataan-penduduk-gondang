<?php

namespace App\Filament\Resources\Marriages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;

class MarriagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('husband.full_name')
                    ->label('Suami')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('wife.full_name')
                    ->label('Istri')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('marriage_certificate_number')
                    ->label('No. Akta Nikah')
                    ->searchable(),
                TextColumn::make('marriage_date')
                    ->label('Tanggal Nikah')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('kua_name')
                    ->label('KUA')
                    ->searchable(),
                TextColumn::make('divorce_certificate_number')
                    ->label('No. Akta Cerai')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('marriage_status')
                    ->label('Status')
                    ->state(fn ($record) => $record->divorce_date ? 'Berakhir' : 'Aktif')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Aktif' => 'success',
                        'Berakhir' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('divorce_date')
                    ->label('Tanggal Cerai')
                    ->date('d M Y')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Diperbarui Pada')
                    ->dateTime('d M Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->striped()
            ->filters([
                SelectFilter::make('marriage_status')
                    ->label('Status Pernikahan')
                    ->options([
                        'active' => 'Aktif',
                        'history' => 'Riwayat',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value'] ?? null) {
                            'active' => $query->whereNull('divorce_date'),
                            'history' => $query->whereNotNull('divorce_date'),
                            default => $query,
                        };
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}