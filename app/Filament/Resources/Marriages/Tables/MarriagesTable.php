<?php

namespace App\Filament\Resources\Marriages\Tables;

use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class MarriagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([

                TextColumn::make('marriage_certificate_number')
                    ->label('No. Akta Nikah')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Nomor akta disalin')
                    ->weight('semibold'),

                TextColumn::make('husband.full_name')
                    ->label('Suami')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('wife.full_name')
                    ->label('Istri')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('marriage_date')
                    ->label('Tanggal Nikah')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('kua_name')
                    ->label('KUA Pencatat')
                    ->toggleable()
                    ->placeholder('-'),

                TextColumn::make('status')
                    ->label('Status')
                    ->state(fn($record) => $record->divorce_date ? 'Bercerai' : 'Aktif')
                    ->badge()
                    ->color(fn(string $state) => $state === 'Bercerai' ? 'danger' : 'success'),

                TextColumn::make('divorce_date')
                    ->label('Tanggal Cerai')
                    ->date('d M Y')
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y, H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])
            ->filters([

                TernaryFilter::make('status')
                    ->label('Status Pernikahan')
                    ->placeholder('Semua status')
                    ->trueLabel('Aktif')
                    ->falseLabel('Bercerai')
                    ->queries(
                        true: fn(Builder $query) => $query->whereNull('divorce_date'),
                        false: fn(Builder $query) => $query->whereNotNull('divorce_date'),
                        blank: fn(Builder $query) => $query,
                    ),

                Filter::make('marriage_date')
                    ->schema([
                        \Filament\Forms\Components\DatePicker::make('from')
                            ->label('Dari Tanggal')
                            ->native(false),
                        \Filament\Forms\Components\DatePicker::make('until')
                            ->label('Sampai Tanggal')
                            ->native(false),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $q, $date) => $q->whereDate('marriage_date', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $q, $date) => $q->whereDate('marriage_date', '<=', $date));
                    }),

            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('marriage_date', 'desc');
    }
}
