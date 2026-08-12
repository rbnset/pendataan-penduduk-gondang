<?php

namespace App\Filament\Resources\Rws\Tables;

use App\Filament\Resources\Rws\Pages\ViewRw;
use App\Models\Rw;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class RwsTable
{
    public static function configure(Table $table): Table
    {
        return $table

            ->columns([

                // =====================================================
                // IDENTITAS
                // =====================================================

                TextColumn::make('number')
                    ->label('RW')
                    ->formatStateUsing(fn($state) => "RW {$state}")
                    ->weight('semibold')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('chairman.full_name')
                    ->label('Ketua RW')
                    ->placeholder('Belum ditentukan')
                    ->searchable(),

                // =====================================================
                // DATA UTAMA
                // =====================================================

                TextColumn::make('rts_count')
                    ->label('Jumlah RT')
                    ->counts('rts')
                    ->formatStateUsing(fn($state) => $state . ' RT')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                // =====================================================
                // DATA TAMBAHAN
                // =====================================================

                TextColumn::make('created_at')
                    ->label('Dibuat')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diperbarui')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

            ])

            ->searchPlaceholder('Cari nomor RW atau nama ketua...')
            ->searchDebounce('500ms')
            ->striped()

            ->modifyQueryUsing(
                fn(Builder $query) => $query->orderBy('number')
            )

            ->paginated([10, 25, 50, 'all'])
            ->defaultPaginationPageOption(25)

            ->recordUrl(
                fn(Rw $record): string => ViewRw::getUrl(['record' => $record])
            )

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
